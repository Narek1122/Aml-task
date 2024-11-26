<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str as IlluminateStr;

class FileService
{
    /**
     * Handle file upload and return the file URL.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param string $disk
     * @param string|null $directory
     * @return string
     */
    public function fileUpload($file, $disk = 'public', $directory = null)
    {
        // Generate a unique file name to avoid collision
        $fileName = $this->generateFileName($file);

        // Determine the full path where the file will be stored
        $filePath = $directory ? $directory . '/' . $fileName : $fileName;

        // Store the file and get the path
        try {
            $path = Storage::disk($disk)->putFileAs($directory, $file, $fileName);
        } catch (\Exception $e) {
            // Log error and rethrow exception
            Log::error("File upload failed: " . $e->getMessage());
            throw $e;
        }

        // Return the full file URL
        return Storage::disk($disk)->url($path);
    }

    /**
     * Generate a unique file name using current timestamp and a random string.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private function generateFileName($file)
    {
        // Get the file extension
        $extension = $file->getClientOriginalExtension();

        // Generate a unique file name (timestamp + random string)
        return Str::random(40) . '_' . time() . '.' . $extension;
    }

    /**
     * Delete a file by its full path (URL).
     *
     * @param string $fullUrl
     * @param string $disk
     * @return bool
     */
    public function deleteFileByFullUrl($fullUrl, $disk = 'public')
    {
        // Extract the relative file path from the full URL
        $relativePath = $this->getFilePathFromUrl($fullUrl);

        // Proceed to delete the file using the relative path
        return $this->deleteFileByPath($relativePath, $disk);
    }

    /**
     * Extract file path from the URL.
     *
     * @param string $url
     * @return string
     */
    private function getFilePathFromUrl($url)
    {
        // Define the base URL part we want to remove
        $baseUrl = env('APP_URL') . '/storage/';

        // Remove the base URL and get the relative path
        $relativePath = str_replace($baseUrl, '', $url);

        return $relativePath;
    }

    /**
     * Delete a file by its full path.
     *
     * @param string $filePath
     * @param string $disk
     * @return bool
     */
    public function deleteFileByPath($filePath, $disk = 'public')
    {
        // Check if the file exists before attempting to delete it
        if (Storage::disk($disk)->exists($filePath)) {
            try {
                // Delete the file
                return Storage::disk($disk)->delete($filePath);
            } catch (\Exception $e) {
                // Log error and return false
                Log::error("File deletion failed: " . $e->getMessage());
                return false;
            }
        }

        // File does not exist
        Log::warning("File not found for deletion: {$filePath}");
        return false;
    }
}
