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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 30)->unique();
            $table->string('status', 20)->default('pending');
            $table->decimal('subtotal', 12, 0);
            $table->decimal('shipping_fee', 12, 0)->default(0);
            $table->decimal('discount_amount', 12, 0)->default(0);
            $table->decimal('total', 12, 0);
            $table->string('shipping_name', 50);
            $table->string('shipping_phone', 20);
            $table->string('shipping_postal_code', 10);
            $table->string('shipping_address', 300);
            $table->string('shipping_address_detail', 200)->nullable();
            $table->string('shipping_memo', 200)->nullable();
            $table->string('payment_method', 30)->default('bank_transfer');
            $table->string('payment_id', 100)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('tracking_number', 50)->nullable();
            $table->string('tracking_url', 500)->nullable();
            $table->text('admin_memo')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
