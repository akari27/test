<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class RamenTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ramen_tags')->insert([
            'name'=>'塩ラーメン',
        ]);
        
        DB::table('ramen_tags')->insert([
            'name'=>'味噌ラーメン',
        ]);
        
        DB::table('ramen_tags')->insert([
            'name'=>'醤油ラーメン',
        ]);
        
        DB::table('ramen_tags')->insert([
            'name'=>'豚骨ラーメン',
        ]);
        
        DB::table('ramen_tags')->insert([
            'name'=>'油そば',
        ]);
    }
}
