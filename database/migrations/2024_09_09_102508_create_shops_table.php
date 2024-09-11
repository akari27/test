<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('menu', 300)->nullable();
            $table->string('reserve', 50)->nullable();
            $table->string('open_time',20)->nullable();
            $table->string('close_time',20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('min_price')->nullable();
            $table->integer('max_price')->nullable();
            $table->double('review_avg')->nullable();
            $table->string('shop_image_url')->nullable();
            //$table->string('location')->nullable();
           //外部キー
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('shop_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};