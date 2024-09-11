<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locations')->insert([
            'address'=>'神奈川県川崎市宮前区犬蔵1-9-14',
            'latitude'=>'35.58777599',
            'longitude'=>'139.563724795',
        ]);
        
        DB::table('locations')->insert([
            'address'=>'神奈川県川崎市宮前区南野川2-1-25',
            'latitude'=>'35.566853807465',
            'longitude'=>'139.601370225',
        ]);
    }
}
