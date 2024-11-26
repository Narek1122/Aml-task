<?php

namespace App\Services;

use App\Repositories\BlogRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Services\FileService;

class BlogService
{
    protected BlogRepository $blogRepository;
    protected FileService $fileService;

    /**
     * BlogService constructor.
     *
     * @param BlogRepository $blogRepository
     * @param FileService $fileService
     */
    public function __construct(BlogRepository $blogRepository, FileService $fileService)
    {
        $this->blogRepository = $blogRepository;
        $this->fileService = $fileService;
    }

    /**
     * Retrieve all blogs (paginated).
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllBlogs(int $perPage = 10)
    {
        try {
            return $this->blogRepository->getAll($perPage);
        } catch (Exception $e) {
            throw new Exception('Error retrieving all blogs: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve blogs for the current authenticated user (paginated).
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     * @throws Exception
     */
    public function getUserBlogs(int $perPage = 10)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                throw new Exception('User not authenticated.');
            }

            return $this->blogRepository->getByUserId($userId, $perPage);
        } catch (Exception $e) {
            throw new Exception('Error fetching user blogs: ' . $e->getMessage());
        }
    }

    /**
     * Create a new blog.
     *
     * @param array $data
     * @return \App\Models\Blog
     */
    public function createBlog(array $data)
    {
        try {
            if (isset($data['image'])) {
                $data['image'] = $this->fileService->fileUpload($data['image']);
            }

            return $this->blogRepository->create($data);
        } catch (Exception $e) {
            throw new Exception('Error creating blog: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing blog.
     *
     * @param int $blogId
     * @param array $data
     * @return \App\Models\Blog
     * @throws Exception
     */
    public function updateBlog(int $blogId, array $data)
    {
        try {
            $blog = $this->blogRepository->findById($blogId);
            $this->authorizeAction($blog);

            // Handle image update and deletion
            if (isset($data['image'])) {
                if ($blog->image) {
                    $this->fileService->deleteFileByFullUrl($blog->image);
                }

                $data['image'] = $this->fileService->fileUpload($data['image']);
            }

            return $this->blogRepository->update($blog, $data);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Blog not found.');
        } catch (Exception $e) {
            throw new Exception('Error updating blog: ' . $e->getMessage());
        }
    }

    /**
     * Delete a blog and its associated file.
     *
     * @param int $blogId
     * @throws Exception
     */
    public function deleteBlog(int $blogId)
    {
        try {
            $blog = $this->blogRepository->findById($blogId);
            $this->authorizeAction($blog);

            // Delete associated file if exists
            if ($blog->image) {
                $this->fileService->deleteFileByPath($blog->image);
            }

            $this->blogRepository->delete($blog);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Blog not found.');
        } catch (Exception $e) {
            throw new Exception('Error deleting blog: ' . $e->getMessage());
        }
    }

    /**
     * Check if the current user is authorized to perform an action on the blog.
     *
     * @param \App\Models\Blog $blog
     * @throws Exception
     */
    protected function authorizeAction($blog)
    {
        if ($blog->user_id !== Auth::id()) {
            throw new Exception('Unauthorized action.');
        }
    }

    /**
     * Retrieve a specific blog by its ID.
     *
     * @param int $id
     * @return \App\Models\Blog
     * @throws NotFoundHttpException
     */
    public function getBlogById(int $id): \App\Models\Blog
    {
        try {
            return $this->blogRepository->findById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Blog not found.');
        } catch (Exception $e) {
            throw new Exception('Error retrieving blog: ' . $e->getMessage());
        }
    }
}
