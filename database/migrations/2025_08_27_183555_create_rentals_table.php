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
        Schema::disableForeignKeyConstraints();

        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_in_store_id')->constrained('article__in__stores');
            $table->foreignId('user_id')->constrained();
            $table->timestamp('rented_at');
            $table->timestamp('returned_at')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
