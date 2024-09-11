<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>toppage</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <style>
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
            .button {
                display: inline-block;
                padding: 10px;
                border: none;
                background-color: #007bff;
                color: white;
                border-radius: 5px;
                text-decoration: none;
                text-align: center;
            }
            .button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
    <x-app-layout>
        <x-slot name="header">
        　TopPage
        </x-slot>
        <div class="user">
        ユーザー名：{{ Auth::user()->name }}
        </div>
        
        <div class="button">
            <a href='/search'>近くのラーメン屋を探す</a>
        </div>
        
        <div class="search-form">
            <form action="/search/result" method="GET">
                <input type="text" name="keyword" placeholder="店名検索" required />
                <button type="submit">検索</button>
            </form>
        </div>
        
        <div class="button">
            <a href='/shops/create'>店を紹介する</a>
        </div>
        <br>
        
        <div class="button">
            <a href="search/second">条件を絞って近くの店を探す</a>
        </div>
        
        
    </x-app-layout>
    </body>
</html>
