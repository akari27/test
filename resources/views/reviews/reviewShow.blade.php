<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>全ての口コミ</title>
    <!-- 必要に応じてスタイルシートを追加 -->
</head>
<body>
<x-app-layout>
    <x-slot name="header">
        show_all_reviews
    </x-slot>
        <h1>{{ $shop->name }}</h1>
        
        <h2>＜　全ての口コミ　＞</h2>　　<a href="{{ route('shops.show', ['shop' => $shop->id]) }}">店詳細ページに戻る</a>
        @foreach($reviews as $review)
            <div class="review">
                <p>投稿者：{{ $review->user->name }}</p>
                <p>{{ $review->body }}</p>
                <p>いいねの数：{{ $review->likes_count }}</p>
                <form action="/reviews/{{ $review->id }}/like" method="POST">
                    @csrf
                    <button type="submit">
                        @if($review->likeBy(Auth::user()))
                            いいねを消す
                        @else
                            いいね
                        @endif
                    </button>
                </form>
            </div>
        @endforeach
</x-app-layout>
</body>
</html>
