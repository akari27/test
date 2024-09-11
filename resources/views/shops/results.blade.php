<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>検索結果</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /*リスト*/
        .shop-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .shop-item h3 {
            margin: 0 0 10px 0;
        }
        .shop-item p {
            margin: 5px 0;
        }
        
        /*検索窓*/
        .search-form {
            margin: 20px 0;
        }
        .search-form input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-form button {
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<x-app-layout>
    <x-slot name="header">
        検索結果
    </x-slot>
    
    <div class="search-form">
        <form action="/search/result" method="GET">
            <input type="text" name="keyword" placeholder="店名検索" value="{{ old('keyword', $keyword) }}" required>
            <button type="submit">検索</button>
        </form>
    </div>
    
    <h1>検索ワード: "{{ $keyword }}"</h1>

    @if($shops->isEmpty())
        <p>該当するラーメン店はありませんでした。</p>
    @else
        @foreach($shops as $shop)
            <div class="shop-item">
                <h3>{{ $shop->name }}</h3>
                <p>評価： {{ $shop->review_avg }}</p>
                <p>ジャンル： {{ $shop->shop_category->name }}</p>
                <p>営業時間： {{ $shop->open_time }} - {{ $shop->close_time }}</p>
                <p>料金： ¥{{ $shop->min_price }} - ¥{{ $shop->max_price }}</p>
                <p>住所: {{ $shop->location->address }}</p>
                <a href="/shops/{{ $shop->id }}">詳細を見る</a>
            </div>
        @endforeach
    @endif

    <div class="footer">
        <a href="/">戻る</a>
    </div>
</x-app-layout>
</body>
</html>
