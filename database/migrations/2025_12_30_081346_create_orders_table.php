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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('order_number', 32)->unique()->nullable();
            $table->string('user_id', 36);
            $table->string('status', 16)->default('PENDING');
            $table->string('payment_method', 32)->nullable();
            $table->string('payment_status', 16)->default('UNPAID');
            $table->json('address_snapshot');
            $table->integer('subtotal_cents');
            $table->integer('discount_cents')->default(0);
            $table->integer('shipping_cents')->default(0);
            $table->integer('total_cents');
            $table->string('note', 255)->nullable();
            $table->dateTime('placed_at');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancel_reason', 100)->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('receipt_no', 50)->unique()->nullable();
            $table->string('midtrans_order_id', 50);
            $table->string('snap_token', 255)->nullable();
            $table->string('snap_redirect_url', 255)->nullable();
            $table->dateTime('snap_token_expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->index(['user_id', 'status']);
            $table->index('placed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
