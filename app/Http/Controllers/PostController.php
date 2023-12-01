<?php

namespace App\Http\Controllers;

use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected Post $post;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(path="/api/getPosts",
     *     tags={"post"},
     *     summary="Get all posts",
     *     description="",
     *     operationId="getPosts",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=1
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
    public function getPosts()
    {
        $posts = $this->post->paginate(10);
        $postsCollection = new PostCollection($posts);
        return response()->json([
            'data' => $postsCollection,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(path="/api/addPost",
     *     tags={"post"},
     *     summary="Add a new post",
     *     description="Add a new post to the application",
     *     operationId="addPost",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="desc", type="string", description="Description of the post"),
     *                  @OA\Property(
     *                      property="img",
     *                      description="Image file",
     *                      type="file",
     *                      format="binary"
     *                  )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="post", type="object",
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="description", type="string", example="Sample description"),
     *                     @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-18 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-18 12:00:00")
     *                 ),
     *                 @OA\Property(property="message", type="string", example="Add Post success")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function addPost(Request $request)
    {
        $dataCreate = $request->all();
        $post = Auth::user()->posts()->create($dataCreate);

        $dataPostCreate = new PostResource($post);
        $res = [
            'data' => $dataPostCreate,
            'message' => 'Add Post success',
        ];
        return response()->json([
            'data' => $res,
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(path="/api/showPost/{id}",
     *     tags={"post"},
     *     summary="Get a specific post by ID",
     *     description="",
     *     operationId="showPosts",
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
    public function showPost(string $id)
    {
        $post = $this->post->findOrFail($id);
        $postResource = new PostResource($post);
        $countLike = $post->likes()->count();
        $countComment = $post->comments()->count();
        $res = [
            'data' => $postResource,
            'countLike' => $countLike,
            'countComment' => $countComment,
        ];
        return response()->json([
            'data' => $res,
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Post(path="/api/updatePost/{id}",
     *     tags={"post"},
     *     summary="Update post",
     *     description="",
     *     operationId="updatePost",
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
     *                   @OA\Property(
     *                       property="img",
     *                       description="Image file",
     *                       type="file",
     *                       format="binary"
     *                   )
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
    public function updatePost(Request $request, string $id)
    {
        $post = $this->post->findOrFail($id);

        $data = $request->all();

        $post->update($data);

        $updatedPost = new PostResource($post);

        return response()->json([
            'data' => $updatedPost,
            'message' => 'Post updated successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(path="/api/deletePost/{id}",
     *     tags={"post"},
     *     summary="Delete post by ID",
     *     description="",
     *     operationId="deletePost",
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
    public function deletePost(string $id)
    {
        $post = $this->post->findOrFail($id);
        $post->delete();
        return response()->json([
            'message' => 'Delete Post success',
        ], Response::HTTP_OK);
    }
}
