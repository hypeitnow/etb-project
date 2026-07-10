<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('three_x_three_tournament_teams', function (Blueprint $table): void {
            $table->foreignId('group_id')
                ->nullable()
                ->after('status')
                ->constrained('three_x_three_tournament_groups')
                ->nullOnDelete();
        });

        Schema::table('three_x_three_tournament_matches', function (Blueprint $table): void {
            $table->string('team_one_placeholder')->nullable()->after('team_two_id');
            $table->string('team_two_placeholder')->nullable()->after('team_one_placeholder');
            $table->unsignedTinyInteger('bracket_round_order')->nullable()->after('round_label');
            $table->unsignedInteger('bracket_position')->nullable()->after('bracket_round_order');
        });
    }

    public function down(): void
    {
        Schema::table('three_x_three_tournament_matches', function (Blueprint $table): void {
            $table->dropColumn([
                'team_one_placeholder',
                'team_two_placeholder',
                'bracket_round_order',
                'bracket_position',
            ]);
        });

        Schema::table('three_x_three_tournament_teams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('group_id');
        });
    }
};
