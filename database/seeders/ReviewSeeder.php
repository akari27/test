<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reviews')->insert([
            'body'=>'おいしかった',
            'review'=>'4',
            'shop_id'=>'1',
            'user_id'=>'1',
            'created_at'=>new DateTime(),
            'updated_at'=>new DateTime(),
        ]);
        
        DB::table('reviews')->insert([
            'body'=>'うまかった',
            'review'=>'3',
            'shop_id'=>'2',
            'user_id'=>'1',
            'created_at'=>new DateTime(),
            'updated_at'=>new DateTime(),
        ]);
    }
}
