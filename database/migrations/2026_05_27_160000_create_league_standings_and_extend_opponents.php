<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opponents', function (Blueprint $table): void {
            if (! Schema::hasColumn('opponents', 'source_team_url')) {
                $table->string('source_team_url')->nullable()->after('logo_path');
            }

            if (! Schema::hasColumn('opponents', 'is_league_team')) {
                $table->boolean('is_league_team')->default(false)->after('source_team_url');
            }
        });

        Schema::create('league_standings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('opponent_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('league_id')->default(215);
            $table->string('season', 20)->default('2025/2026');
            $table->unsignedInteger('position');
            $table->unsignedInteger('points');
            $table->unsignedInteger('games');
            $table->unsignedInteger('wins');
            $table->unsignedInteger('losses');
            $table->unsignedInteger('home_wins');
            $table->unsignedInteger('home_losses');
            $table->unsignedInteger('away_wins');
            $table->unsignedInteger('away_losses');
            $table->unsignedInteger('points_for');
            $table->unsignedInteger('points_against');
            $table->integer('points_difference');
            $table->decimal('ratio', 8, 4);
            $table->string('source_team_name');
            $table->string('source_team_url')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['league_id', 'season', 'opponent_id']);
            $table->index(['league_id', 'season', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_standings');

        Schema::table('opponents', function (Blueprint $table): void {
            if (Schema::hasColumn('opponents', 'is_league_team')) {
                $table->dropColumn('is_league_team');
            }

            if (Schema::hasColumn('opponents', 'source_team_url')) {
                $table->dropColumn('source_team_url');
            }
        });
    }
};
