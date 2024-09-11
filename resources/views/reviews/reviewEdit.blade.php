<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Posts</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    </head>
    <body>
    <x-app-layout>
        <x-slot name="header">
        　ShowReview
        </x-slot>
        <!--<h1>{ $review->shop->name }}</h1>-->
        <h1>{{ $review->shop ? $review->shop->name : 'Shop not available' }}</h1>

        
        <div class="review">
            <h2>＜　評価　＞</h2>
            {{ $review->review }}
        </div>
        
        <div class="body">
            <h2>＜　口コミ　＞</h2>
            {{ $review->body }}
        </div>
        
        <div class="image">
            <h2>＜ 画像　＞</h2>
            <img src="{{ $review->review_image_url }}" alt="画像の投稿はありません。">
        </div>
        
        <a href='/reviews/{{ $review->id }}/edit'>編集する</a>
        
        <form action="/reviews/{{ $review->id }}" id="form_{{ $review->id }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" onclick="deleteReview({{ $review->id }})">口コミの削除</button>
        </form>
        
        <div class="show">
            <a href="{{ route('shops.show', ['shop' => $shop->id]) }}">店の詳細ページに戻る</a>
        </div>
            
        <div class="footer">
            <a href="/">トップページに戻る</a>
        </div>
        
        <script>
            function deleteReview(id){
                `use strict`
                
                if(confirm('削除すると復元できません。 \n本当に削除しますか？')){
                    document.getElementById(`form_${id}`).submit();
                }
            }
        </script>
    </x-app-layout>
    </body>
</html>