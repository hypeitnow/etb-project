<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_section_images', function (Blueprint $table): void {
            $table->text('caption')->nullable()->after('alt');
        });
    }

    public function down(): void
    {
        Schema::table('club_section_images', function (Blueprint $table): void {
            $table->dropColumn('caption');
        });
    }
};
