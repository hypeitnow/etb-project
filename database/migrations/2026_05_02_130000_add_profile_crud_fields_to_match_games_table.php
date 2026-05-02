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

        Schema::table('match_games', function (Blueprint $table) {
            if (! Schema::hasColumn('match_games', 'opponent_name')) {
                $table->string('opponent_name')->nullable();
            }

            if (! Schema::hasColumn('match_games', 'is_home')) {
                $table->boolean('is_home')->default(true);
            }

            if (! Schema::hasColumn('match_games', 'our_score')) {
                $table->unsignedSmallInteger('our_score')->nullable();
            }

            if (! Schema::hasColumn('match_games', 'opponent_score')) {
                $table->unsignedSmallInteger('opponent_score')->nullable();
            }

            if (! Schema::hasColumn('match_games', 'opponent_logo')) {
                $table->string('opponent_logo')->nullable();
            }

            if (! Schema::hasColumn('match_games', 'status')) {
                $table->string('status')->default('upcoming')->index();
            }
        });

        if (Schema::hasColumn('match_games', 'opponent')) {
            DB::table('match_games')
                ->whereNull('opponent_name')
                ->update(['opponent_name' => DB::raw('opponent')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('match_games')) {
            return;
        }

        Schema::table('match_games', function (Blueprint $table) {
            $columns = [
                'opponent_name',
                'is_home',
                'our_score',
                'opponent_score',
                'opponent_logo',
                'status',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('match_games', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
