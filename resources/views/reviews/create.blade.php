<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>口コミ投稿ページ</title>
    </head>
    <body>
    <x-app-layout>
        <x-slot name="header">
            CreateReview
        </x-slot>
        
            <form action="/reviews" method="POST" enctype="multipart/form-data">{{--要確認--}}
                @csrf
                
                <input type="hidden" name="post[shop_id]" value="{{ $shop->id }}" />
                
                <div class="review">
                    <h2>評価</h2>
                    <input type="number" name="post[review]" min="1" max="5" />{{--name属性について--}}
                </div>
                
                <div class="comment">
                    <h2>口コミ</h2>
                    <textarea name="post[body]" placeholder="お好きなコメントをどうぞ" >{{ old('post.body') }}</textarea>
                    <p class="body__error" style="color:red">{{ $errors->first('post.body') }}</p>
                </div>
                
                <div class="image">
                    <h2>画像</h2>
                    <input type="file" name="review_image">
                </div>
                
                <input type="submit" value="保存"/>
                
            </form>
            <div class="footer">
                <a href="/shops/{{ $shop->id }}">戻る</a>
    
    </x-app-layout>
    </body>
</html>