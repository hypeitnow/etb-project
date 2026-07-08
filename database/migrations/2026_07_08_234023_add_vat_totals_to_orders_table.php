<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('total_net_grosze')->default(0)->after('total_grosze');
            $table->unsignedBigInteger('total_vat_grosze')->default(0)->after('total_net_grosze');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_net_grosze', 'total_vat_grosze']);
        });
    }
};
