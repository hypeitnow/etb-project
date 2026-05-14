<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('three_x_three_members', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_coach')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('three_x_three_members');
    }
};
