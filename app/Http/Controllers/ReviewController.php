<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Cloudinary;

class ReviewController extends Controller
{
    public function create(Shop $shop){
        return view('reviews.create',[
            'shop' => $shop,
        ]);
    } 
    
    public function reviewEdit(Review $review){
        $shop = $review->shop;
        
        return view('reviews.reviewEdit',[
            'review' => $review,
            'shop' => $shop,
        ]);
    }
    
    public function edit2(Shop $shop, Review $review){
        return view('reviews.edit2',[
            'shop' => $shop,
            'review' => $review,
        ]);
    }
    
    public function reviewShow(Shop $shop) {
        // 全ての口コミを「いいね」の数でソートして取得
        $reviews = $shop->reviews()->withCount('likes') // 口コミに「いいね」の数をカウント
                                  ->orderBy('likes_count', 'desc') // 「いいね」の数で降順にソート
                                  ->get();
        return view('reviews.reviewShow', [
            'shop' => $shop,
            'reviews' => $reviews
        ]);
    }
    
    public function update2(ReviewRequest $request,Review $review){
        $input=$request['post'];
        
        $currentImageUrl = $review->review_image_url;
        
        if($request->file('review_image')){ //画像ファイルが送られた時だけ処理実行
            $image_url = Cloudinary::upload($request->file('review_image')->getRealPath())->getSecurePath();
            $input['review_image_url'] = $image_url; 
        } else {
            $input['review_image_url']=$currentImageUrl;
        }
        
        $review->update($input);
        
        return redirect("/reviews/{$review->id}/show/");
    }
    
    public function delete2(Review $review){
        $review->delete();
        return redirect("/shops/{$review->shop_id}/show");
    }
    
    public function store(ReviewRequest $request, Review $review){//要検討
        //auth()->user()メソッドを使用してログイン中のユーザーオブジェクトを取得しidを取得
        $userId = auth()->user()->id;
        $input = $request['post'];
        
        if($request->file('review_image')){ //画像ファイルが送信されたときのみ実行
            $image_url = Cloudinary::upload($request->file('review_image')->getRealPath())->getSecurePath();
            $input += ['review_image_url' => $image_url];
        }
        
        $input['user_id']=$userId;
        
        $review->fill($input)->save();
        
        return redirect("/reviews/{$review->id}/show/");
    }
}