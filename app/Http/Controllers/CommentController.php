<?php

namespace App\Http\Controllers;

use App\Http\Resources\Comment\CommentCollection;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected Comment $comment;

    /**
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @OA\Get(path="/api/getComment",
     *     tags={"comment"},
     *     summary="Get all comments",
     *     description="",
     *     operationId="getComment",
     *     security={{"sanctum": {}}},
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
    public function getComment()
    {
        $comments = $this->comment->paginate(5);
        $commentsCollection = new CommentCollection($comments);
        return response()->json([
            'data' => $commentsCollection,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(path="/api/addComment/{id}",
     *     tags={"comment"},
     *     summary="Add comment for post",
     *     description="",
     *     operationId="addComment",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="ID of the post",
     *           required=true,
     *           @OA\Schema(
     *               type="string"
     *           )
     *       ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   @OA\Property(property="desc", type="string", description="Description comment"),
     *               )
     *           )
     *      ),
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
    public function addComment(Request $request, $postId)
    {
        $dataCreate = $request->all();
        $user = Auth::user();
        // Tìm bài đăng cụ thể
        $post = Post::find($postId);
        if (!$post) {
            return response()->json([
                'error' => 'Post not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Tạo bình luận mới
        $comment = new Comment([
            'desc' => $dataCreate['desc'],
            'user_id' => $user->id,
            'post_id' => $postId,
        ]);
        $comment->save();
        // Trả về thông báo thành công cùng với thông tin bình luận vừa tạo
        $dataCommentCreate = new CommentResource($comment);
        $res = [
            'data' => $dataCommentCreate,
            'message' => 'Add Comment success',
        ];
        return response()->json([
            'data' => $res,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(path="/api/updateComment/{id}",
     *     tags={"comment"},
     *     summary="Update comment for post",
     *     description="",
     *     operationId="updateComment",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           description="ID of the comment",
     *           required=true,
     *           @OA\Schema(
     *               type="string"
     *           )
     *       ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   @OA\Property(property="desc", type="string", description="Description comment"),
     *               )
     *           )
     *      ),
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
    public function updateComment(Request $request, string $id)
    {
        // Tìm bình luận cần cập nhật
        $comment = $this->comment->findOrFail($id);

        // Kiểm tra xem người dùng hiện tại có quyền cập nhật bình luận hay không
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'error' => 'You are not authorized to update this comment',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $dataUpdate = $request->all();

        // Cập nhật dữ liệu của bình luận
        $comment->update($dataUpdate);

        // Trả về thông báo thành công cùng với thông tin bình luận đã được cập nhật
        $dataCommentUpdate = new CommentResource($comment);
        $res = [
            'data' => $dataCommentUpdate,
            'message' => 'Update Comment success',
        ];
        return response()->json([
            'data' => $res,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(path="/api/deleteComment/{id}",
     *     tags={"comment"},
     *     summary="Delete comment by ID",
     *     description="",
     *     operationId="deleteComment",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the post",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
    public function deleteComment(string $id)
    {
        $comment = $this->comment->findOrFail($id);
        $comment->delete();
        return response()->json([
            'message' => 'Delete Comment success',
        ], Response::HTTP_OK);
    }
}
