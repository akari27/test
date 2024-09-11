<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\RamenTag;
use App\Models\Review;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests\ShopRequest;
use Cloudinary;
use App\Services\GeocodingService;

class ShopController extends Controller
{
    protected $geocodingService;
    
    public function __construct(GeocodingService $geocodingService){
        $this->geocodingService = $geocodingService;
    }
    
    // トップページを表示
    public function index()
    {
        return view('shops.index');
    }
    
    public function show(Shop $shop) {
        // 必要なリレーションをロード
        $shop->load(['shop_category', 'ramen_tags', 'reviews' => function ($query) {
            $query->withCount('likes')//いいねの数をカウント
                  ->orderBy('likes_count','desc')//数に応じて降順にソート
                  ->take(3);//3件取得
        }, 'location']);
        
        // レビュー平均を更新
        $shop->updateReviewAvg();
        
        // 緯度と経度を取得（例としてlocationテーブルにlatitudeとlongitudeカラムがあると仮定）
        $latitude = $shop->location->latitude;
        $longitude = $shop->location->longitude;
        
        // Google Maps APIキーを設定
        $api_key = config('services.google_maps.key'); // 環境変数や設定ファイルからAPIキーを取得
        
        // ビューにデータを渡す
        return view('shops.show', [
            'shop' => $shop,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'api_key' => $api_key
        ]);
    }
    
    // 検索ページを表示
    public function search(ShopCategory $shopcategory, RamenTag $ramentag, Review $review) {
        return view('shops.search', [
            'categories' => $shopcategory->get(),
            'tags' => $ramentag->get(),
            'reviews' => $review->all(),
        ]);
    }
    
    // マップ結果を表示するためのメソッド
    public function mapResults(Request $request) {
        $categoryId = $request->input('category');
        $ramenTags = $request->input('ramen_tags', []);
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
    
        $query = Shop::query();
    
        if ($categoryId) {
            $query->where('shop_category_id', $categoryId);
        }
    
        if (!empty($ramenTags)) {
            $query->whereHas('ramen_tags', function($q) use ($ramenTags) {
                $q->whereIn('ramen_tags.id', $ramenTags);
            });
        }
    
        if ($latitude && $longitude) {
            $query->selectRaw('shops.*, 
                (6371 * acos(cos(radians(?)) * cos(radians(locations.latitude)) * cos(radians(locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(locations.latitude)))) AS distance', 
                [$latitude, $longitude, $latitude])
                ->join('locations', 'shops.location_id', '=', 'locations.id')
                ->orderBy('distance')
                ->having('distance', '<', 5); // 5km以内の店舗を取得
        }
    
        $shops = $query->with('location', 'shop_category')->get();
        $api_key = config('app.api_key');
    
        return view('shops.mapresult', [
            'shops' => $shops,
            'api_key' => $api_key,
            'categoryId' => $categoryId,
            'ramenTags' => $ramenTags,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }

    // マップ検索結果を返すためのメソッド（JSON形式）
    public function mapSearch(Request $request) {
        $categoryId = $request->input('category');
        $ramenTags = $request->input('ramen_tags', []);
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
    
        $query = Shop::query();
    
        if ($categoryId) {
            $query->where('shop_category_id', $categoryId);
        }
    
        if (!empty($ramenTags)) {
            $query->whereHas('ramen_tags', function($q) use ($ramenTags) {
                $q->whereIn('ramen_tags.id', $ramenTags);
            });
        }
    
        if ($latitude && $longitude) {
            $query->selectRaw('shops.*, 
                (6371 * acos(cos(radians(?)) * cos(radians(locations.latitude)) * cos(radians(locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(locations.latitude)))) AS distance', 
                [$latitude, $longitude, $latitude])
                ->join('locations', 'shops.location_id', '=', 'locations.id')
                ->orderBy('distance')
                ->having('distance', '<', 5); // 5km以内の店舗を取得
        }
    
        $shops = $query->with('location', 'shop_category')->get();
        
        // 取得したショップのデータを整形
        $result = $shops->map(function($shop) {
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'open_time' => $shop->open_time,
                'close_time' => $shop->close_time,
                'min_price' => $shop->min_price,
                'max_price' => $shop->max_price,
                'review_avg' => $shop->review_avg,
                'category_name' => $shop->shop_category ? $shop->shop_category->name : '未分類',
                'latitude' => $shop->location->latitude,
                'longitude' => $shop->location->longitude,
                'distance' => $shop->distance
            ];
        });
        
        $api_key = config('app.api_key');
        
        return response()->json($result);
    }
    
    // 検索結果を表示するためのメソッド
    public function searchResults(Request $request) {
        $keyword = $request->input('keyword');
        $shops = Shop::where('name', 'like', '%' .$keyword . '%')->get();
        return view('shops.results', ['shops' => $shops, 'keyword' => $keyword]);
    }
    
    // 店舗の詳細ページを表示するためのメソッド
    public function showEdit(Shop $shop){
        $shop->load(['shop_category','ramen_tags','reviews','location']);
        // レビュー平均を更新
        $shop->updateReviewAvg();
        return view('shops.showEdit',['shop' => $shop]);
    }
    
    // 店舗編集ページを表示するためのメソッド
    public function edit(Shop $shop, ShopCategory $shopcategory, RamenTag $ramentag, Review $review){
        return view('shops.edit',[
            'shop' => $shop,
            'categories' => $shopcategory->get(),
            'tags' => $ramentag->get(),
            'reviews' => $review->all(),
        ]);
    }
    
    // 店舗情報を更新するためのメソッド
    public function update(ShopRequest $request, Shop $shop) {
        $input = $request->input('shop');
        
        $locationAddress = $request->input('shop.location');
        $location = null;
        
        if($locationAddress){
            $coordinates = $this->geocodingService->getCoordinates($locationAddress);
            // あたらしいLocationインスタンスを作成し、保存
            if($coordinates) {
                $location = Location::create([
                    'address' => $locationAddress,
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                ]);
            } else {
                $location = Location::create([
                    'address' => $locationAddress,   
                ]);
            }
        }
        
        if($location) {
            $input['location_id'] = $location->id;
        } else {
            $input['location_id'] = null;
        }
        
        $currentImageUrl = $shop->shop_image_url;
        
        if($request->file('shop_image')) { // 画像ファイルが送られた時だけ処理実行
            $image_url = Cloudinary::upload($request->file('shop_image')->getRealPath())->getSecurePath();
            $input['shop_image_url'] = $image_url; 
        } else {
            $input['shop_image_url'] = $currentImageUrl;
        }
        
        $input_tags = $request->input('shop_tags_array',[]);
        
        $shop->ramen_tags()->sync($input_tags);
        
        $shop->update($input);
        
        return redirect("/shops/{$shop->id}/show/");
    }
    
    // 店舗作成ページを表示するためのメソッド
    public function create(ShopCategory $shopcategory, RamenTag $ramentag, Review $review){
        return view('shops.create', [
            'categories' => $shopcategory->get(),
            'tags' => $ramentag->get(),
            'reviews' => $review->all(),
        ]);
    }
    
    // 店舗を削除するためのメソッド
    public function delete(Shop $shop){
        $shop->delete();
        return redirect('/');
    }

    public function store(ShopRequest $request, Shop $shop){
        //auth()->user()メソッドを使用してログイン中のユーザーオブジェクトを取得しidを取得
        $userId = auth()->user()->id;
        
        $input = $request->input('shop');
        $locationAddress = $request->input('shop.location');
        
        $location = null;
        
        if($locationAddress){
            $coordinates = $this->geocodingService->getCoordinates($locationAddress);
            //あたらしいLocationインスタンスを作成し、保存
            if($coordinates) {
                $location = Location::create([
                    'address' => $locationAddress,
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                ]);
            } else {
                $location = Location::create([
                    'address' => $locationAddress,   
                ]);
            }
        }
        
        if($location) {
            $input['location_id'] = $location->id;
        } else {
            $input['location_id'] = null;
        }
        
        if($request->file('shop_image')){ //画像ファイルが送られた時だけ処理実行
            $image_url = Cloudinary::upload($request->file('shop_image')->getRealPath())->getSecurePath();
            $input += ['shop_image_url' => $image_url];
        }
        
        $input_tags = $request->input('tags_array',[]);
        $input['user_id'] = $userId;
        
        $shop->fill($input)->save();
        $shop->ramen_tags()->sync($input_tags);
        
        //return view('shops.showEdit')->with(['shop' => $shop]);
        return redirect("/shops/{$shop->id}/show/");
    }
    
    
}