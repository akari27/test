<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable=[
        'body',
        'review',
        'review_image_url',
        'shop_id',//これも保存処理いるかも
        'user_id',
    ];
    
    protected static function boot(){
        parent::boot();
        
        static::saved(function ($review) {
            // レビューが保存された後、関連するショップのレビュー平均を更新
            if($review->shop){
                $review->shop->updateReviewAvg();
            }
        });

        static::deleted(function ($review) {
            // レビューが削除された後、関連するショップのレビュー平均を更新
            if($review->shop){
                $review->shop->updateReviewAvg();
            }
        });
    }
    
    public function likes(){
        return $this->hasMany(Like::class);
    }
    
    public function likeBy(User $user){//特定のユーザがこのレビューに対していいねしているか確認するメソッド
        return $this->likes()->where('user_id',$user->id)->exists();
    }
    
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function users(){
        return $this->belongsToMany(User::class);
    }
}