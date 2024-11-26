<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    VerificationController,
    BlogController
};

// Authentication Routes
Route::prefix('')->group(function () {
    /**
     * User Registration
     */
    Route::post('/register', [AuthController::class, 'register']);

    /**
     * User Login
     */
    Route::post('/login', [AuthController::class, 'login']);

    /**
     * Email Verification
     */
    Route::get('/verify/{token}', [AuthController::class, 'verifyEmail']);

    // Email Resend Verification Route
    Route::get('email/verify/{id}', [VerificationController::class, 'verify'])
        ->name('verification.verify');
    Route::get('email/resend', [VerificationController::class, 'resend'])
        ->name('verification.resend');
});

// Blog Routes (No Authentication Required)
Route::get('/blogs', [BlogController::class, 'index'])
    ->name('blogs.index');  // Get all blogs (paginated)

// Group all routes that require authentication
Route::middleware('auth:api')->group(function () {

    // Authenticated User Routes
    Route::get('/me', [AuthController::class, 'me']); // Get the authenticated user
    Route::post('/logout', [AuthController::class, 'logout']); // User logout

    // Blog Routes for Authenticated Users
    Route::prefix('blogs')->group(function () {
        /**
         * Get blogs for the authenticated user
         */
        Route::get('/user', [BlogController::class, 'userBlogs'])
            ->name('blogs.user');

        /**
         * Create a new blog post
         */
        Route::post('/', [BlogController::class, 'store'])
            ->name('blogs.store');

        /**
         * Get a specific blog by ID
         */
        Route::get('/{id}', [BlogController::class, 'show'])
            ->name('blogs.show')
            ->where('id', '[0-9]+');

        /**
         * Update a specific blog by ID
         */
        Route::post('/{id}', [BlogController::class, 'update'])
            ->name('blogs.update')
            ->where('id', '[0-9]+');
        // Route::patch('/{id}', [BlogController::class, 'update'])
        //     ->name('blogs.update')
        //     ->where('id', '[0-9]+');

        /**
         * Delete a specific blog by ID
         */
        Route::delete('/{id}', [BlogController::class, 'destroy'])
            ->name('blogs.destroy')
            ->where('id', '[0-9]+');
    });

});
