<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Resources\Json\BlogResource;
use App\Services\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    protected BlogService $blogService;

    /**
     * BlogController constructor.
     *
     * @param BlogService $blogService
     */
    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Get a paginated list of all blogs.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $perPage = request()->query('per_page', 10);
            $blogs = $this->blogService->getAllBlogs($perPage);

            return response()->json([
                'status' => 'success',
                'data' => BlogResource::collection($blogs),
                'meta' => [
                    'current_page' => $blogs->currentPage(),
                    'per_page' => $blogs->perPage(),
                    'total' => $blogs->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Get a paginated list of blogs for the authenticated user.
     *
     * @return JsonResponse
     */
    public function userBlogs(): JsonResponse
    {
        try {
            $perPage = request()->query('per_page', 10);
            $blogs = $this->blogService->getUserBlogs($perPage);

            return response()->json([
                'status' => 'success',
                'data' => BlogResource::collection($blogs),
                'meta' => [
                    'current_page' => $blogs->currentPage(),
                    'per_page' => $blogs->perPage(),
                    'total' => $blogs->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Create a new blog.
     *
     * @param BlogStoreRequest $request
     * @return JsonResponse
     */
    public function store(BlogStoreRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()?->user()?->id ?? null;
            $blog = $this->blogService->createBlog($data);

            return response()->json([
                'status' => 'success',
                'data' => new BlogResource($blog),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Update an existing blog.
     *
     * @param BlogUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(BlogUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $blog = $this->blogService->updateBlog($id, $request->validated());

            return response()->json([
                'status' => 'success',
                'data' => new BlogResource($blog),
            ], 200);
        } catch (NotFoundHttpException $e) {
            return response()->json(['status' => 'error', 'message' => 'Blog not found.'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Delete a blog.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->blogService->deleteBlog($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Blog deleted successfully.',
            ], 204);
        } catch (NotFoundHttpException $e) {
            return response()->json(['status' => 'error', 'message' => 'Blog not found.'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Show a specific blog by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $blog = $this->blogService->getBlogById($id);

            return response()->json([
                'status' => 'success',
                'data' => new BlogResource($blog),
            ], 200);
        } catch (NotFoundHttpException $e) {
            return response()->json(['status' => 'error', 'message' => 'Blog not found.'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle exceptions and return JSON response.
     *
     * @param \Exception $e
     * @return JsonResponse
     */
    protected function handleException(\Exception $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
