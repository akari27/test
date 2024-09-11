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
        　Show
        </x-slot>
        <h1 class="name">{{ $shop->name }}</h1>
        
        <div class="shop_information">
        <h2>＜　店情報　＞</h2>
            <div class="reserve">
                <h3>予約可否：　</h3>{{ $shop->reserve }}
            </div>
            
            <div class="address">
                <h3>住所：　</h3>{{ $shop->location->address }}
            </div>
            
            <div class="time">
                <h3>営業時間：　</h3>{{ $shop->open_time }}～{{ $shop->close_time }}
            </div>
            
            <div class="phone">
                <h3>電話番号：　</h3>{{ $shop->phone }}
            </div>
            
            <div class="price">
                <h3>料金：　</h3>{{ $shop->min_price }}円～{{ $shop->max_price }}円
            </div>  
            
            <div class="category">
                <h3>ラーメンの系統：　</h3>{{ $shop->shop_category->name }}
            </div>
            
            <div class="tag">
                <h3>ラーメンの種類：　</h3>
                @foreach($shop->ramen_tags as $tag)
                    {{ $tag->name }}@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>
        
        <div class="menu">
            <h2>＜　メニュー　＞</h2>
            {{ $shop->menu }}
        </div>
        
        <!--<div class="map">{{--編集--}}-->
        <!--    -->
        <!--</div>-->
        
        <div class="image">
            <h2>＜　画像　＞</h2>
            <img src="{{ $shop->shop_image_url }}" alt="画像の投稿はありません。" >
        </div>
        
        <a href='/shops/{{ $shop->id }}/edit'>編集する</a>
        
        <a href="/reviews/create/{{$shop->id}}">評価と口コミも書く</a>{{--後でreviewControllerから作業--}}
        
        <form action="/shops/{{ $shop->id }}" id="form_{{ $shop->id }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" onclick="deleteShop({{ $shop->id }})">この投稿を削除する</button>
        </form>
        
        <div class="footer">
            <a href="/">トップページに戻る</a>
        </div>
        
        <script>
            function deleteShop(id){
                'use strict'
                
                if (confirm('削除すると復元できません。 \n本当に削除しますか？')){
                    document.getElementById(`form_${id}`).submit();
                }
            }
        </script>
    </x-app-layout>
    </body>
</html>