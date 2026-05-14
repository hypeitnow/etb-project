<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table): void {
            $table->text('description')->nullable()->after('photo_path');
            $table->boolean('publish_description')->default(false)->after('description');
        });

        Schema::table('news', function (Blueprint $table): void {
            $table->string('excerpt', 500)->nullable()->after('content');
            $table->boolean('is_visible')->default(true)->after('publish_at');
        });

        Schema::table('match_games', function (Blueprint $table): void {
            $table->string('season')->nullable()->after('status');
            $table->text('notes')->nullable()->after('publish_at');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table): void {
            $table->dropColumn(['description', 'publish_description']);
        });

        Schema::table('news', function (Blueprint $table): void {
            $table->dropColumn(['excerpt', 'is_visible']);
        });

        Schema::table('match_games', function (Blueprint $table): void {
            $table->dropColumn(['season', 'notes']);
        });
    }
};
