<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fan_profiles', function (Blueprint $table): void {
            $table->boolean('marketing_email_consent')->default(false)->after('can_buy_merch');
        });

        Schema::table('pending_registrations', function (Blueprint $table): void {
            $table->boolean('marketing_email_consent')->default(false)->after('accepted_privacy');
        });
    }

    public function down(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table): void {
            $table->dropColumn('marketing_email_consent');
        });

        Schema::table('fan_profiles', function (Blueprint $table): void {
            $table->dropColumn('marketing_email_consent');
        });
    }
};
