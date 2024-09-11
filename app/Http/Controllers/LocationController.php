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
        
        $ramens = Location::select('locations.*')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS calculated_distance', [$latitude, $longitude, $latitude])
            ->with(['shops' => function($query) {
                $query->select('id', 'location_id', 'name', 'open_time', 'close_time', 'min_price', 'max_price', 'review_avg')
                    ->with(['shop_category:id,name']);
            }])
            ->fromSub(function ($query) use ($latitude, $longitude) {
                $query->select('locations.*')
                    ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS calculated_distance', [$latitude, $longitude, $latitude])
                    ->from('locations');
            }, 'locations')
            ->where('calculated_distance', '<', 100)
            ->orderBy('calculated_distance')  // 修正: calculated_distance で並び替え
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
                        'review_avg' => $shop->review_avg,
                        'category_name' => $shop->shop_category->name ?? '未分類',
                    ];
                }),
            ];
        });
        
        return response()->json($result);
    }
}