<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('shops')->insert([
             'id' => '1',
             'name'=>'武蔵家　東名川崎店',
             'reserve'=>'不可',
             'menu' => 'チャーシュー麵',
             'open_time'=>'11:00',
             'close_time'=>'23:00',
             'phone'=>'044-977-0090',
             'min_price'=>'500',
             'max_price'=>'1500',
             'created_at'=>new DateTime(),
             'updated_at'=>new DateTime(),
             'review_avg'=>'4',
             'location_id'=>'1',
             'shop_category_id'=>'4',
             'user_id'=>'1',
             
        ]);
        
        DB::table('shops')->insert([
             'id'=>'2',
             'name'=>'ラーメン大桜　川崎野川店',
             'reserve'=>'不可',
             'open_time'=>'11:00',
             'close_time'=>'22:00',
             'menu' => 'ザクピリ',
             'phone'=>'044-863-7326',
             'min_price'=>'500',
             'max_price'=>'1500',
             'created_at'=>new DateTime(),
             'updated_at'=>new DateTime(),
             'review_avg'=>'3',
             'location_id'=>'2',
             'shop_category_id'=>'4',
             'user_id'=>'1',
        ]);
    }
}
