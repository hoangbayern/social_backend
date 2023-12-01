<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected Like $like;

    /**
     * @param Like $like
     */
    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    /**
     * @OA\Get(path="/api/getLikes/{postId}",
     *     tags={"like"},
     *     summary="Get count like for post",
     *     description="",
     *     operationId="getLikes",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *           name="postId",
     *           in="path",
     *           description="ID of the post",
     *           required=true,
     *           @OA\Schema(
     *               type="string"
     *           )
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Schema(type="string"),
     *         @OA\Header(
     *             header="X-Rate-Limit",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             ),
     *             description="calls per hour allowed by the user"
     *         ),
     *         @OA\Header(
     *             header="X-Expires-After",
     *             @OA\Schema(
     *                 type="string",
     *                 format="date-time",
     *             ),
     *             description="date in UTC when token expires"
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid username/password supplied")
     * )
     */
    public function getLikes($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $likes = $post->likes()->get();
        $countLike = $post->likes()->count();

        $userIds = $likes->pluck('user_id')->toArray();

        $data = [
            'user_id' => $userIds,
            'countLike' => $countLike,
        ];

        return response()->json([
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(path="/api/addLike/{postId}",
     *     tags={"like"},
     *     summary="Add like for post",
     *     description="",
     *     operationId="addLike",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *           name="postId",
     *           in="path",
     *           description="ID of the post",
     *           required=true,
     *           @OA\Schema(
     *               type="string"
     *           )
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Schema(type="string"),
     *         @OA\Header(
     *             header="X-Rate-Limit",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             ),
     *             description="calls per hour allowed by the user"
     *         ),
     *         @OA\Header(
     *             header="X-Expires-After",
     *             @OA\Schema(
     *                 type="string",
     *                 format="date-time",
     *             ),
     *             description="date in UTC when token expires"
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid username/password supplied")
     * )
     */
    public function addLike($postId)
    {
        $user = Auth::user();
        $post = Post::findOrFail($postId);
        $existingLike = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();
        if ($existingLike) {
            $existingLike->delete();
            return response()->json([
                'message' => 'Post has been unliked',
            ], Response::HTTP_OK);
        } else {
            $like = new Like([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            $like->save();
            return response()->json([
                'message' => 'Post has been liked',
            ], Response::HTTP_CREATED);
        }
    }
}
