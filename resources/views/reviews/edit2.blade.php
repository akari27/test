<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>口コミ編集</title>
    </head>
    <body>
    <x-app-layout>
        <x-slot name="header">
        　TopPage
        </x-slot>
            <h1>{{ $review->shop ? $review->shop->name : 'Shop not available' }}</h1>  
            <form action="/reviews/{{ $review->id }}/show" method="POST" enctype="multipart/form-data">{{--要確認--}}
                @csrf
                @method('PUT')
                
                <div class="review">
                    <h2>評価</h2>
                    <input type="number" name="post[review]" min="1" max="5" value={{ $review->review }} ></input>{{--name属性について--}}
                </div>
                
                <div class="comment">
                    <h2>口コミ</h2>
                    <textarea name="post[body]" placeholder="お好きなコメントをどうぞ">{{ $review->body }}</textarea>
                    <p class="body__error" style="color:red">{{ $errors->first('post.body') }}</p>
                </div>
                
                <div class="image">
                    <h2>画像</h2>
                    <input type="file" name=review_image value="{{ $review->review_image_url }}">
                </div>
                
                <input type="submit" value="保存"/>
                
            </form>
           
    </x-app-layout>
    </body>
</html>