<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('pending_payment');
            $table->unsignedBigInteger('total_grosze');
            $table->unsignedBigInteger('shipping_grosze')->default(0);
            $table->string('shipping_method')->nullable();
            $table->json('shipping_address')->nullable();
            $table->string('tracking_number')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('idempotency_key')->nullable()->unique();
            $table->string('payment_session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
