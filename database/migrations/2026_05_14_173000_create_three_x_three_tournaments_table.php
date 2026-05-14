<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('three_x_three_tournaments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('status')->default('upcoming');
            $table->string('organizer')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('three_x_three_tournaments');
    }
};
