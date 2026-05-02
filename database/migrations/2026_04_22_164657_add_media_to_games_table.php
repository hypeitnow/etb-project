<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('home_logo')->nullable();
            $table->string('away_logo')->nullable();
            $table->string('stream_link')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'image',
                'home_logo',
                'away_logo',
                'stream_link',
            ]);
        });
    }
};
