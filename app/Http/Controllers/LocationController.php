<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Shop;

class LocationController extends Controller
{
    public function search(Request $request) {
        $locations = Location::with(['shops.shop_category'])->get(); // 全ての場所とそのショップ、ショップカテゴリを取得

        $api_key = config('app.google_maps_api_key');
        // dd($locations);
        
        return view('locations.search')->with([
            'api_key' => $api_key,
            'locations' => $locations,
        ]);
    }
    
    public function getNearRamen(Request $request) {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        
        $ramens = Location::with(['shops' => function($query) use ($latitude, $longitude) {
            $query->selectRaw('shops.*,
            (6371 * acos(cos(radians(?)) * cos(radians(locations.latitude)) * cos(radians(locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(locations.latitude)))) AS distance',
            [$latitude, $longitude, $latitude])
            ->join('locations', 'shops.location_id', '=', 'locations.id')
            ->orderBy('distance');
        }, 'shops.shop_category'])
            ->selectRaw('locations.*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<', 100)
            ->orderBy('distance')
            ->limit(4)
            ->get();
        
        $result = $ramens->map(function($ramen) {
            return [
                'latitude' => (float)$ramen->latitude,
                'longitude' => (float)$ramen->longitude,
                'distance' => $ramen->distance,
                'address' => $ramen->address,
                'shops' => $ramen->shops->map(function($shop) {
                    return [
                        'id' => $shop->id,
                        'name' => $shop->name,
                        'open_time' => $shop->open_time,
                        'close_time' => $shop->close_time,
                        'min_price' => $shop->min_price,
                        'max_price' => $shop->max_price,
                        'review_avg' => $shop->review_avg, // 小数点第1位までフォーマット
                        'category_name' => $shop->shop_category ? $shop->shop_category->name : '未分類',
                    ];
                }),
            ];
        });
        
        return response()->json($result);
    }
}