<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User; // Import the User model

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the database is connected
        if (DB::connection()->getDatabaseName()) {
            // Check if the 'users' table exists
            if (Schema::hasTable('users')) {
                // Insert two default users if not already present
                if (User::count() == 0) {
                    DB::table('users')->insert([
                        [
                            'name' => 'John Doe',
                            'email' => 'johndoe@gmail.com',
                            'password' => Hash::make('12345678'),
                            'email_verified_at' => now(), // Set the email as verified
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'name' => 'Jane Doe',
                            'email' => 'janedoe@example.com',
                            'password' => Hash::make('password123'),
                            'email_verified_at' => now(), // Set the email as verified
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                }
            } else {
                // Log or handle the case where the 'users' table does not exist
                // echo "The 'users' table does not exist.";
            }
        } else {
            // Log or handle the case where the database is not connected
            // echo "Database connection failed.";
        }
    }
}
