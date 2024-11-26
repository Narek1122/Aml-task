<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user and send an email verification link.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        // Register the user and send verification email
        $user = $this->userService->createUser($request->validated());

        // Send a response with a message (you can replace this with an actual mail send logic)
        return response()->json(['message' => 'Registration successful. Please check your email to verify your account.'], 201);
    }


    /**
     * Login a user and return a JWT token.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        // Get the user by email
        $user = $this->userService->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Email not verified'], 401);
        }

        // Attempt to generate the JWT token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Logout a user and invalidate the JWT token.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out.'], 200);
    }

    /**
     * Get the authenticated user.
     *
     * This method returns the currently authenticated user in a JSON format.
     * If the user is not authenticated, it will return a 401 Unauthorized response with a message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): \Illuminate\Http\JsonResponse
    {
        // Retrieve the authenticated user
        $user = auth()->user();

        // If user is authenticated, return user data as JSON with status and message
        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'Authenticated user retrieved successfully.',
                'data' => $user
            ]);
        }

        // If no user is authenticated, return 401 Unauthorized with status and message
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized. No authenticated user found.',
        ], 401);
    }
}
