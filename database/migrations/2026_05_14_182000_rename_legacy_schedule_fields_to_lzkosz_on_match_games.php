<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('match_games')) {
            return;
        }

        $legacyIncludeColumn = 'include_in_'.'u'.'zkosz';
        $legacyRoundColumn = 'u'.'zkosz_round';

        if (Schema::hasColumn('match_games', $legacyIncludeColumn) && ! Schema::hasColumn('match_games', 'include_in_lzkosz')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->renameColumn('include_in_'.'u'.'zkosz', 'include_in_lzkosz');
            });
        }

        if (Schema::hasColumn('match_games', $legacyRoundColumn) && ! Schema::hasColumn('match_games', 'lzkosz_round')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->renameColumn('u'.'zkosz_round', 'lzkosz_round');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('match_games')) {
            return;
        }

        $legacyIncludeColumn = 'include_in_'.'u'.'zkosz';
        $legacyRoundColumn = 'u'.'zkosz_round';

        if (Schema::hasColumn('match_games', 'include_in_lzkosz') && ! Schema::hasColumn('match_games', $legacyIncludeColumn)) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->renameColumn('include_in_lzkosz', 'include_in_'.'u'.'zkosz');
            });
        }

        if (Schema::hasColumn('match_games', 'lzkosz_round') && ! Schema::hasColumn('match_games', $legacyRoundColumn)) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->renameColumn('lzkosz_round', 'u'.'zkosz_round');
            });
        }
    }
};
