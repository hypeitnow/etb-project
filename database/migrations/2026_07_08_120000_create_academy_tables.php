<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code', 40)->unique();
            $table->string('color', 7)->default('#facc15');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academy_trainers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academy_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('academy_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academy_group_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('academy_trainings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('academy_group_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->string('trainer_name')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 30)->default('scheduled');
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();

            $table->index(['starts_at', 'status']);
        });

        Schema::create('academy_calendar_notes', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->timestamps();

            $table->index(['starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_calendar_notes');
        Schema::dropIfExists('academy_trainings');
        Schema::dropIfExists('academy_messages');
        Schema::dropIfExists('academy_trainers');
        Schema::dropIfExists('academy_groups');
    }
};
