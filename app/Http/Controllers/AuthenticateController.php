<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authenticates\LoginAuthRequest;
use App\Http\Requests\Authenticates\RegisterAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class AuthenticateController extends Controller
{
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(path="/api/login",
     *     tags={"authenticate"},
     *     summary="Logs user into the system",
     *     description="",
     *     operationId="loginUser",
     *     @OA\Parameter(
     *         name="email",
     *         required=true,
     *         in="query",
     *         description="The user name for login",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *         description="The password for login",
     *     ),
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
    public function login(LoginAuthRequest $request)
    {
        if (Auth::attempt(
                $request->only('email', 'password')
        )) {
            $user = Auth::user();
            $success['token'] = $user->createToken('api_token')->plainTextToken;

            return response()->json(
                [
                    'success login' => $success,
                    'token_type' => 'Bearer',
                    'data' => $user,
                ],Response::HTTP_OK);
        }
        else {
            return response()->json(
                [
                    'error' => 'Unauthorised'
                ], Response::HTTP_UNAUTHORIZED);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(path="/api/register",
     *     tags={"authenticate"},
     *     summary="Register user into the system",
     *     description="",
     *     operationId="registerUser",
     *     @OA\Parameter(
     *         name="email",
     *         required=true,
     *         in="query",
     *         description="The user name for login",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *         description="The password for login",
     *     ),
     *    @OA\Parameter(
     *        name="name",
     *        required=true,
     *        in="query",
     *        @OA\Schema(
     *            type="string",
     *         ),
     *         description="The password for login",
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *          ),
     *          description="The password for login",
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
    public function register(RegisterAuthRequest $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('api_token')->plainTextToken;
        $success['name'] = $user->name;

        $res = [
            'data' => $user,
            'token' => $success,
            'token_type' => 'Bearer',
        ];
        return response()->json(
            [
                'success' => $res,
            ],Response::HTTP_CREATED);
    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return \response()->json([
            'success' => $user
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(path="/api/logout",
     *     tags={"authenticate"},
     *     summary="Logout user",
     *     description="",
     *     operationId="logoutUser",
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
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Revoke all tokens for the authenticated user
        $user->tokens()->delete();

        return response()->json(
            [
                'message' => 'User logged out successfully'
            ],
            Response::HTTP_OK
        );
    }
}
