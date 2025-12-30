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
        Schema::create('books', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->string('category_id', 36);
            $table->string('author_id', 36)->nullable();
            $table->string('isbn', 32)->nullable();
            $table->integer('price_cents');
            $table->integer('discount_price_cents')->nullable();
            $table->integer('stock')->default(0);
            $table->string('cover_url', 255);
            $table->text('description');
            $table->integer('pages')->nullable();
            $table->string('language', 40)->nullable();
            $table->string('publisher', 160)->nullable();
            $table->date('published_at')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('author_id')->references('id')->on('authors');

            $table->index(['category_id', 'is_active']);
            $table->index('title');
            $table->index('isbn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
