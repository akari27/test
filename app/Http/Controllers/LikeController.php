// <?php

// namespace App\Http\Controllers;

// use App\Models\Post;
// use App\Models\PostLike;
// use Illuminate\Http\Request;

// class LikeController extends Controller
// {

//     public function likePost(Request $request)
//     {
//         $user_id = \Auth::id();
//         //jsのfetchメソッドで記事のidを送信しているため受け取ります。
//         $post_id = $request->post_id;
//         //自身がいいね済みなのか判定します
//         $alreadyLiked = PostLike::where('user_id', $user_id)->where('post_id', $post_id)->first();

//         if (!$alreadyLiked) {
//         //こちらはいいねをしていない場合の処理です。つまり、post_likesテーブルに自身のid（user_id）といいねをした記事のid（post_id）を保存する処理になります。
//             $like = new PostLike();
//             $like->post_id = $post_id;
//             $like->user_id = $user_id;
//             $like->save();
//         } else {
//             //すでにいいねをしていた場合は、以下のようにpost_likesテーブルからレコードを削除します。
//             PostLike::where('post_id', $post_id)->where('user_id', $user_id)->delete();
//         }
//         //ビューにその記事のいいね数を渡すため、いいね数を計算しています。
//         $post = Post::where('id', $post_id)->first();
//         $likesCount = $post->likes->count();

//         $param = [
//             'likesCount' =>  $likesCount,
//         ];
//         //ビューにいいね数を渡しています。名前は上記のlikesCountとなるため、フロントでlikesCountといった表記で受け取っているのがわかると思います。
//         return response()->json($param);
//     }
// }

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Review;
use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(Review $review, User $user){
        $userId = auth()->user()->id;
        
        $like = Like::where('review_id',$review->id)->where('user_id',$userId)->first();
        //review_idとuser_idに基づいて、既存のいいねを検索する
        
        if($like){
            $like->delete();
        } else {
            Like::create([
                'review_id' => $review->id,
                'user_id' => $userId,
            ]);
        }   
        
        $shopId=$review->shop_id;
        
        return redirect()->route('shops.show', $shopId);
    }
}