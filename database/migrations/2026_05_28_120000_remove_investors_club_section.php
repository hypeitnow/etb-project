<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('club_sections')->where('slug', 'investors')->delete();
    }

    public function down(): void
    {
        DB::table('club_sections')->insertOrIgnore([
            'slug' => 'investors',
            'title' => 'Inwestorzy',
            'body' => null,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
