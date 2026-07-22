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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('number')->unique();
            $table->string('seller_name');
            $table->string('seller_address');
            $table->string('seller_nip')->nullable();
            $table->string('buyer_name');
            $table->string('buyer_address');
            $table->string('buyer_nip')->nullable();
            $table->unsignedInteger('total_net_grosze');
            $table->unsignedInteger('total_vat_grosze');
            $table->unsignedInteger('total_gross_grosze');
            $table->string('pdf_path')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
