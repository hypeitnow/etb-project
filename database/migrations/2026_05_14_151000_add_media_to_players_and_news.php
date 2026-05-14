<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table): void {
            if (! Schema::hasColumn('players', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('weight');
            }
        });

        Schema::table('news', function (Blueprint $table): void {
            if (! Schema::hasColumn('news', 'main_image_path')) {
                $table->string('main_image_path')->nullable()->after('publish_at');
            }
        });

        Schema::create('news_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('path');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_images');

        Schema::table('news', function (Blueprint $table): void {
            if (Schema::hasColumn('news', 'main_image_path')) {
                $table->dropColumn('main_image_path');
            }
        });

        Schema::table('players', function (Blueprint $table): void {
            if (Schema::hasColumn('players', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
        });
    }
};
