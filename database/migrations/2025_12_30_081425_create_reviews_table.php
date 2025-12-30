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
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36);
            $table->string('book_id', 36);
            $table->integer('rating');
            $table->string('title', 120)->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'book_id']);
            $table->index(['book_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
