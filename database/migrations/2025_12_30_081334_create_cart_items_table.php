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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('cart_id', 36);
            $table->string('book_id', 36);
            $table->integer('quantity');
            $table->integer('price_cents_at_add');
            $table->timestamps();

            $table->unique(['cart_id', 'book_id']);
            $table->foreign('cart_id')->references('id')->on('carts');
            $table->foreign('book_id')->references('id')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
