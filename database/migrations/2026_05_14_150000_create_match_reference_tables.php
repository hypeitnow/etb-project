<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sports_halls', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('opponents', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        Schema::table('match_games', function (Blueprint $table): void {
            if (! Schema::hasColumn('match_games', 'sports_hall_id')) {
                $table->foreignId('sports_hall_id')
                    ->nullable()
                    ->after('location')
                    ->constrained('sports_halls')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('match_games', 'opponent_id')) {
                $table->foreignId('opponent_id')
                    ->nullable()
                    ->after('opponent_name')
                    ->constrained('opponents')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('match_games', 'home_logo')) {
                $table->string('home_logo')->nullable()->after('opponent_logo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('match_games', function (Blueprint $table): void {
            if (Schema::hasColumn('match_games', 'opponent_id')) {
                $table->dropConstrainedForeignId('opponent_id');
            }

            if (Schema::hasColumn('match_games', 'sports_hall_id')) {
                $table->dropConstrainedForeignId('sports_hall_id');
            }

            if (Schema::hasColumn('match_games', 'home_logo')) {
                $table->dropColumn('home_logo');
            }
        });

        Schema::dropIfExists('opponents');
        Schema::dropIfExists('sports_halls');
    }
};
