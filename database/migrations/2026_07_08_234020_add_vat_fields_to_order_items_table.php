<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('vat_rate')->default(23)->after('unit_price_grosze');
            $table->unsignedInteger('net_price_grosze')->default(0)->after('vat_rate');
            $table->unsignedInteger('gross_price_grosze')->default(0)->after('net_price_grosze');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['vat_rate', 'net_price_grosze', 'gross_price_grosze']);
        });
    }
};
