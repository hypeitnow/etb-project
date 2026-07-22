<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaCardService
{
    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $data
     */
    public function create(string $modelClass, array $data, ?UploadedFile $photo, string $directory): Model
    {
        if ($photo) {
            $data['photo_path'] = $photo->store($directory, 'public');
        }

        return $modelClass::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data, ?UploadedFile $photo, string $directory): Model
    {
        if ($photo) {
            $path = (string) $model->getAttribute('photo_path');

            if ($path !== '') {
                Storage::disk('public')->delete($path);
            }

            $data['photo_path'] = $photo->store($directory, 'public');
        }

        $model->update($data);

        return $model;
    }

    public function delete(Model $model): void
    {
        $path = (string) $model->getAttribute('photo_path');

        if ($path !== '') {
            Storage::disk('public')->delete($path);
        }

        $model->delete();
    }
}
