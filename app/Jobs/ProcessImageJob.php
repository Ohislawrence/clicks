<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public string $disk = 'public',
        public array $sizes = [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 600, 'height' => 400],
            'large' => ['width' => 1200, 'height' => 800],
        ],
        public int $quality = 85
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $fullPath = Storage::disk($this->disk)->path($this->filePath);

            if (!file_exists($fullPath)) {
                Log::error('Image file not found for processing', ['path' => $fullPath]);
                return;
            }

            // Optimize original image
            $image = Image::read($fullPath);

            // Encode with quality settings (progressive JPEG for web optimization)
            $pathInfo = pathinfo($fullPath);
            $extension = strtolower($pathInfo['extension']);

            if ($extension === 'jpg' || $extension === 'jpeg') {
                $image->toJpeg($this->quality)->save($fullPath);
            } elseif ($extension === 'png') {
                $image->toPng()->save($fullPath);
            } elseif ($extension === 'webp') {
                $image->toWebp($this->quality)->save($fullPath);
            }

            // Generate thumbnails and variants
            foreach ($this->sizes as $sizeName => $dimensions) {
                $this->createThumbnail($fullPath, $sizeName, $dimensions);
            }

            Log::info('Image processed successfully', [
                'path' => $this->filePath,
                'sizes_generated' => array_keys($this->sizes),
            ]);

        } catch (\Exception $e) {
            Log::error('Image processing failed', [
                'path' => $this->filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Create and save thumbnail with Intervention Image
     */
    protected function createThumbnail(string $originalPath, string $sizeName, array $dimensions): void
    {
        try {
            $pathInfo = pathinfo($originalPath);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $sizeName . '.' . $pathInfo['extension'];

            // Load and resize image
            $image = Image::read($originalPath);

            // Scale to fit within dimensions while maintaining aspect ratio
            $image->scale(
                width: $dimensions['width'],
                height: $dimensions['height']
            );

            // Save with appropriate format and quality
            $extension = strtolower($pathInfo['extension']);
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $image->toJpeg($this->quality)->save($thumbnailPath);
            } elseif ($extension === 'png') {
                $image->toPng()->save($thumbnailPath);
            } elseif ($extension === 'webp') {
                $image->toWebp($this->quality)->save($thumbnailPath);
            } elseif ($extension === 'gif') {
                $image->toGif()->save($thumbnailPath);
            } else {
                $image->save($thumbnailPath);
            }

            Log::info('Thumbnail created', [
                'original' => $originalPath,
                'thumbnail' => $thumbnailPath,
                'size' => $sizeName,
            ]);

        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed', [
                'original' => $originalPath,
                'size' => $sizeName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Image processing job permanently failed', [
            'path' => $this->filePath,
            'error' => $exception->getMessage(),
        ]);
    }
}
