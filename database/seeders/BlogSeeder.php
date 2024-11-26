<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class BlogSeeder extends Seeder
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
                // Retrieve all users
                $users = DB::table('users')->get();

                // Check if the 'blogs' table exists
                if (Schema::hasTable('blogs')) {
                    // Create an instance of Faker to generate random data
                    $faker = Faker::create();

                    // Loop through each user and insert 15 blog posts
                    foreach ($users as $user) {
                        for ($i = 0; $i < 15; $i++) {
                            // Generate random blog data
                            DB::table('blogs')->insert([
                                'title' => $faker->sentence,
                                'content' => $faker->paragraph,
                                'image' => 'https://loremflickr.com/400/400?random=' . $i + 1,
                                'user_id' => $user->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                } else {
                    echo "The 'blogs' table does not exist.";
                }
            } else {
                echo "The 'users' table does not exist.";
            }
        } else {
            echo "Database connection failed.";
        }
    }
}
