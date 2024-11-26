<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    /**
     * Create a new user in the database.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(array $data): User
    {
        try {
            return User::create($data);
        } catch (\Exception $e) {
            Log::error('Error creating user', ['data' => $data, 'error' => $e->getMessage()]);
            throw new \Exception('Error creating user.');
        }
    }

    /**
     * Find a user by their email address.
     *
     * @param  string  $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning('User not found by email', ['email' => $email]);
        }

        return $user;
    }

    /**
     * Find a user by their email verification token.
     *
     * @param  string  $token
     * @return \App\Models\User|null
     */
    public function findByVerificationToken(string $token): ?User
    {
        return User::where('email_verification_token', $token)->first();
    }

    /**
     * Find a user by their ID, or fail if not found.
     *
     * @param  int  $userId
     * @return \App\Models\User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $userId): User
    {
        try {
            return User::findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            Log::error('User not found by ID', ['user_id' => $userId]);
            throw $e;  // Rethrow to handle at a higher level (controller)
        }
    }

    /**
     * Update a user's details.
     *
     * @param  int  $userId
     * @param  array  $data
     * @return \App\Models\User
     */
    public function update(int $userId, array $data): User
    {
        $user = $this->findOrFail($userId);
        $user->update($data);

        return $user;
    }

    /**
     * Delete a user by ID.
     *
     * @param  int  $userId
     * @return bool
     */
    public function delete(int $userId): bool
    {
        $user = $this->findOrFail($userId);
        return $user->delete();
    }
}
