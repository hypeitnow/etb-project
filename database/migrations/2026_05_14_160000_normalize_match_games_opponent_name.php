<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('match_games')) {
            return;
        }

        if (! Schema::hasColumn('match_games', 'opponent_name')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->string('opponent_name')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('match_games', 'opponent')) {
            DB::table('match_games')
                ->whereNull('opponent_name')
                ->update([
                    'opponent_name' => DB::raw('opponent'),
                ]);
        }

        DB::table('match_games')
            ->whereNull('opponent_name')
            ->orWhere('opponent_name', '')
            ->update([
                'opponent_name' => 'Nieznany przeciwnik',
            ]);

        if (Schema::hasColumn('match_games', 'opponent')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->dropColumn('opponent');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('match_games')) {
            return;
        }

        if (! Schema::hasColumn('match_games', 'opponent')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->string('opponent')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('match_games', 'opponent_name')) {
            DB::table('match_games')
                ->whereNull('opponent')
                ->update([
                    'opponent' => DB::raw('opponent_name'),
                ]);
        }
    }
};
