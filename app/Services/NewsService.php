<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $gallery
     */
    public function create(array $data, int $authorId, ?UploadedFile $mainImage, array $gallery): News
    {
        $data['is_visible'] = (bool) ($data['is_visible'] ?? true);

        if ($mainImage) {
            $data['main_image_path'] = $mainImage->store('news/main', 'public');
        }

        $news = News::query()->create([
            ...$data,
            'author_id' => $authorId,
        ]);

        $this->storeGallery($news, $gallery);

        return $news;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $gallery
     */
    public function update(News $news, array $data, ?UploadedFile $mainImage, array $gallery): News
    {
        $data['is_visible'] = (bool) ($data['is_visible'] ?? false);

        if ($mainImage) {
            if ($news->main_image_path) {
                Storage::disk('public')->delete($news->main_image_path);
            }

            $data['main_image_path'] = $mainImage->store('news/main', 'public');
        }

        $news->update($data);
        $this->storeGallery($news, $gallery);

        return $news;
    }

    public function delete(News $news): void
    {
        if ($news->main_image_path) {
            Storage::disk('public')->delete($news->main_image_path);
        }

        foreach ($news->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $news->delete();
    }

    public function publishNow(News $news): News
    {
        $news->update([
            'is_visible' => true,
            'publish_at' => now(),
        ]);

        return $news;
    }

    /**
     * @param  array<int, UploadedFile>  $gallery
     */
    private function storeGallery(News $news, array $gallery): void
    {
        $existingCount = $news->images()->count();

        foreach (array_slice($gallery, 0, 15 - $existingCount) as $index => $image) {
            $news->images()->create([
                'path' => $image->store('news/gallery', 'public'),
                'sort_order' => $existingCount + $index,
            ]);
        }
    }
}
