<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    /**
     * Get all blogs with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAll(int $perPage = 10)
    {
        return Blog::with('user')->orderByDesc('created_at')->latest()->paginate($perPage);
    }

    /**
     * Get blogs by user ID with pagination.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getByUserId(int $userId, int $perPage = 10)
    {
        return Blog::where('user_id', $userId)->orderByDesc('created_at')->latest()->paginate($perPage);
    }

    /**
     * Find a blog by ID.
     *
     * @param int $id
     * @return \App\Models\Blog
     */
    public function findById(int $id)
    {
        return Blog::findOrFail($id);
    }

    /**
     * Create a new blog.
     *
     * @param array $data
     * @return \App\Models\Blog
     */
    public function create(array $data)
    {
        return Blog::create($data);
    }

    /**
     * Update a blog.
     *
     * @param \App\Models\Blog $blog
     * @param array $data
     * @return \App\Models\Blog
     */
    public function update(Blog $blog, array $data)
    {
        $blog->update($data);
        return $blog;
    }

    /**
     * Delete a blog.
     *
     * @param \App\Models\Blog $blog
     * @return void
     */
    public function delete(Blog $blog)
    {
        $blog->delete();
    }

}
