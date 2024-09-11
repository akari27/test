<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>投稿</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

       
    </head>
    <body>
    <x-app-layout>
        <x-slot name="header">
        　CreateShops
        </x-slot>
        <h1　class="title">ラーメン屋の紹介</h1>
        
        <form action="/shops" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="name">
                <h2>ラーメン屋名</h2>
                <input type="text" name="shop[name]" placeholder="ラーメン屋名" value="{{ old('shop.name') }}"></input>
                <p class="name__error" style="color:red">{{$errors->first('shop.name')}}</p>
            </div>
            
            <div class="shop_informations"> 
                <h2>店情報</h2>
                
                <div class="menu">
                    <h3>メニュー</h3>
                    <textarea name="shop[menu]" placeholder="食べたメニューなど自由にどうぞ"></textarea>
                </div>
                
                <div class="location">
                    <h3>住所</h3>
                    <textarea name="shop[location]" ></textarea>
                </div>
                
                <div class="reserve">
                    <h3>予約可否</h2>
                    <input type="text" name="shop[reserve]" placeholder="可 or 不可"/>
                </div>
                
                <div class="time">
                    <h3>営業時間</h3>
                    <div class="open_time">
                        <input type="text" name="shop[open_time]" placeholder=11時 />～
                    </div>
                    
                    
                    <div class="close_time">
                        <input type="text" name="shop[close_time]" placeholder=23時 />
                    </div>
                </div>
                
                <div class="phone">
                    <h3>電話番号</h3>
                    <input type="text" name="shop[phone]" placeholder="000-000-0000"/>
                </div>
                
                <div class="price">
                    <h3>価格</h3>
                    <div class="min_price">
                        <input type="number" name="shop[min_price]" placeholder="1000"/>円～
                    </div>
                    
                     <div class="max_price">
                        <input type="number" name="shop[max_price]" placeholder="2000"/>円
                    </div>
                </div>
                
                <div class="category">
                    <h3>ラーメンの系統</h3>
                    <select name="shop[shop_category_id]">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="tag">
                    <h3>ラーメンの種類</h3>
                    @foreach($tags as $tag)
                        <label>
                            <input type="checkbox" value="{{ $tag->id }}" name="tags_array[]">
                                {{ $tag->name }}
                            </input>
                        </label>
                    @endforeach
                </div>
                
            </div>
            
            <div class="image">
                <h2>画像</h2>
                <input type="file" name="shop_image">
            </div>
            
            <input type="submit" value="保存"/>
            
        </form>
        <div class="footer">
            <a href="/">戻る</a>
        </div>
    </x-app-layout>
    </body>
</html>
