<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('match_games', 'include_in_lzkosz')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->boolean('include_in_lzkosz')->default(false)->after('season');
            });
        }

        if (! Schema::hasColumn('match_games', 'lzkosz_round')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->string('lzkosz_round')->nullable()->after('include_in_lzkosz');
            });
        }

        if (! Schema::hasColumn('match_games', 'is_ticketed')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->boolean('is_ticketed')->default(false)->after('notes');
            });
        }

        if (! Schema::hasColumn('match_games', 'ticket_url')) {
            Schema::table('match_games', function (Blueprint $table): void {
                $table->string('ticket_url')->nullable()->after('is_ticketed');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['include_in_lzkosz', 'lzkosz_round', 'is_ticketed', 'ticket_url'],
            fn (string $column): bool => Schema::hasColumn('match_games', $column)
        ));

        if ($columns !== []) {
            Schema::table('match_games', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
