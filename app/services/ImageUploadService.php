<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class ImageUploadService
{
    /**
     * Upload và resize một ảnh đơn lẻ vào thư mục chỉ định.
     *
     * @param  UploadedFile  $file      File ảnh từ request
     * @param  string        $folder    Thư mục đích (vd: 'categories', 'blogs', 'banners')
     * @param  int|null      $width     Chiều rộng tối đa sau khi resize (null = không resize)
     * @param  int           $quality   Chất lượng nén (0-100)
     * @param  string        $format    Định dạng đầu ra: 'jpeg' | 'webp'
     * @param  string|null   $prefix    Tiền tố tên file (vd: 'banner_', 'review_')
     * @return string                   Đường dẫn tương đối lưu trong DB
     */
    public function upload(
        UploadedFile $file,
        string $folder,
        ?int $width = 1000,
        int $quality = 80,
        string $format = 'jpeg',
        ?string $prefix = null
    ): string {
        $ext = $format === 'webp' ? 'webp' : 'jpg';
        $prefix = $prefix ?? '';
        $filename = $prefix . time() . '_' . uniqid() . '.' . $ext;
        $path = $folder . '/' . $filename;

        $mimeType = $file->getMimeType();

        // SVG và file không phải raster image -> lưu thẳng, không qua Intervention
        if (!str_starts_with($mimeType, 'image/') || $mimeType === 'image/svg+xml') {
            $origExt = $file->getClientOriginalExtension();
            $filename = $prefix . time() . '_' . uniqid() . '.' . $origExt;
            $path = $folder . '/' . $filename;
            Storage::disk('public')->putFileAs($folder, $file, $filename);
            return $path;
        }

        try {
            $image = Image::read($file);

            if ($width !== null) {
                $image->scale(width: $width);
            }

            $encodedImage = match ($format) {
                'webp'  => $image->toWebp($quality),
                default => $image->toJpeg($quality),
            };

            Storage::disk('public')->put($path, (string) $encodedImage);
        } catch (\Exception $e) {
            Log::error('ImageUploadService::upload – ' . $e->getMessage());
            // Fallback: lưu file gốc không qua Intervention
            $origExt = $file->getClientOriginalExtension();
            $filename = $prefix . time() . '_' . uniqid() . '.' . $origExt;
            $path = $folder . '/' . $filename;
            Storage::disk('public')->putFileAs($folder, $file, $filename);
        }

        return $path;
    }

    /**
     * Upload nhiều ảnh cùng lúc (dùng cho review, product images, hoàn hàng...).
     *
     * @param  UploadedFile[]  $files
     * @param  string          $folder
     * @param  int|null        $width
     * @param  int             $quality
     * @param  string          $format
     * @param  string|null     $prefix
     * @return string[]
     */
    public function uploadMultiple(
        array $files,
        string $folder,
        ?int $width = 1000,
        int $quality = 80,
        string $format = 'jpeg',
        ?string $prefix = null
    ): array {
        return array_map(
            fn($file) => $this->upload($file, $folder, $width, $quality, $format, $prefix),
            $files
        );
    }

    /**
     * Xóa một ảnh khỏi storage (nếu tồn tại).
     *
     * @param  string|null  $path  Đường dẫn tương đối lưu trong DB
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Xóa nhiều ảnh cùng lúc.
     *
     * @param  string[]  $paths
     */
    public function deleteMultiple(array $paths): void
    {
        foreach ($paths as $path) {
            $this->delete($path);
        }
    }

    /**
     * Thay ảnh cũ bằng ảnh mới: xóa cũ -> upload mới.
     *
     * @param  UploadedFile  $file
     * @param  string|null   $oldPath   Đường dẫn ảnh cũ cần xóa
     * @param  string        $folder
     * @param  int|null      $width
     * @param  int           $quality
     * @param  string        $format
     * @param  string|null   $prefix
     * @return string   Đường dẫn ảnh mới
     */
    public function replace(
        UploadedFile $file,
        ?string $oldPath,
        string $folder,
        ?int $width = 1000,
        int $quality = 80,
        string $format = 'jpeg',
        ?string $prefix = null
    ): string {
        $this->delete($oldPath);
        return $this->upload($file, $folder, $width, $quality, $format, $prefix);
    }
}

