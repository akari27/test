<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>条件検索</title>
</head>
<body>
    <x-app-layout>
        <x-slot name="header">
            検索ページ
        </x-slot>
        <div class="search">
            <h1>＜ 条件を絞って近くのラーメン屋を探す ＞</h1>
            <form action="{{ route('shops.mapResults') }}" method="GET">
                <label for="category">ジャンル:</label>
                <select name="category" id="category">
                    <option value="">すべて</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <br>

                <label for="ramen_tags">ラーメンの種類:</label>
                <div id="ramen_tags">
                    @foreach($tags as $tag)
                        <label>
                            <input type="checkbox" name="ramen_tags[]" value="{{ $tag->id }}" />
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
                
                <br>
                <button type="submit">検索</button>
            </form>
        </div>
    </x-app-layout>
</body>
</html>
