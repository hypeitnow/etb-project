<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->string('image')->nullable()->after('home_logo');
            $table->string('stream_link')->nullable()->after('image');
        });

        $gamesData = DB::table('games')->get();
        foreach ($gamesData as $game) {
            DB::table('match_games')->insert([
                'opponent_name' => $game->opponent,
                'match_date' => $game->match_date,
                'location' => $game->location,
                'is_home' => $game->is_home,
                'image' => $game->image ?? null,
                'home_logo' => $game->home_logo ?? null,
                'opponent_logo' => $game->away_logo ?? null,
                'stream_link' => $game->stream_link ?? null,
                'status' => 'finished',
                'created_at' => $game->created_at ?? now(),
                'updated_at' => $game->updated_at ?? now(),
            ]);
        }

        Schema::dropIfExists('games');

        Schema::dropIfExists('matches');

        Schema::rename('match_games', 'matches');
    }

    public function down(): void
    {
        Schema::rename('matches', 'match_games');

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('opponent');
            $table->dateTime('match_date');
            $table->string('location');
            $table->boolean('is_home')->default(true);
            $table->timestamps();
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('opponent');
            $table->dateTime('match_date');
            $table->string('location');
            $table->string('result', 50)->nullable();
            $table->timestamps();
        });

        $matchGamesData = DB::table('match_games')->get();
        foreach ($matchGamesData as $mg) {
            DB::table('games')->insert([
                'opponent' => $mg->opponent_name,
                'match_date' => $mg->match_date,
                'location' => $mg->location,
                'is_home' => $mg->is_home ?? true,
                'created_at' => $mg->created_at,
                'updated_at' => $mg->updated_at,
            ]);
        }
    }
};
