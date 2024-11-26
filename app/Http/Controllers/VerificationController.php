<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        // Inject the UserService dependency.
        $this->userService = $userService;
    }

    /**
     * Verify the user's email address using a valid signed URL.
     *
     * @param int $user_id
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(int $user_id, Request $request)
    {
        // Check if the request has a valid signature.
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Invalid or expired URL provided.',
                'errors' => [
                    'signature' => 'The provided URL signature is either invalid or expired.'
                ]
            ], 401); // 401 Unauthorized
        }

        // Fetch the user using the injected service.
        $user = $this->userService->find($user_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'errors' => [
                    'user' => 'No user was found with the provided ID.'
                ]
            ], 404); // 404 Not Found
        }

        // Check if the user has already verified their email.
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect('/login?email-verified=1');
    }

    /**
     * Resend the email verification link to the authenticated user.
     *
     * @return JsonResponse
     */
    public function resend(): JsonResponse
    {
        $user = Auth::user();

        // Check if the authenticated user has already verified their email.
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
                'errors' => [
                    'email_verified' => 'The user\'s email address has already been verified.'
                ]
            ], 400); // 400 Bad Request
        }

        // Send the email verification notification to the user.
        $user->sendEmailVerificationNotification();

        // Return a success response with a message.
        return response()->json([
            'message' => 'Email verification link sent.',
            'data' => [
                'email' => $user->email,
                'email_verified' => false
            ]
        ], 200); // 200 OK
    }
}
