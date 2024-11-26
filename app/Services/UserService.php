<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserService
{
    protected UserRepository $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user, hash the password, and send an email verification.
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function createUser(array $data): User
    {
        try {
            // Ensure the password is hashed
            $data['password'] = Hash::make($data['password']);

            // Create the user
            $user = $this->userRepository->create($data);

            // Send email verification notification
            $user->sendEmailVerificationNotification();

            return $user;
        } catch (Exception $e) {
            // Log the error for debugging and future reference
            Log::error('User registration failed', ['error' => $e->getMessage(), 'data' => $data]);

            // Rethrow the exception or return a custom error message
            throw new Exception("Registration failed. Please try again later.");
        }
    }

    /**
     * Find a user by email for login purposes.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        try {
            return $this->userRepository->findByEmail($email);
        } catch (ModelNotFoundException $e) {
            Log::error('User not found by email', ['email' => $email, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Find a user by their ID.
     *
     * @param int $userId
     * @return User
     * @throws ModelNotFoundException
     */
    public function find(int $userId): User
    {
        try {
            return $this->userRepository->findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            Log::error('User not found', ['user_id' => $userId, 'error' => $e->getMessage()]);
            throw new ModelNotFoundException("User not found.");
        }
    }
}
