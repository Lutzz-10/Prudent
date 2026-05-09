<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_create_budgets_table.php
public function up(): void
{
    Schema::create('budgets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        $table->decimal('amount', 15, 2);
        $table->tinyInteger('month'); // 1-12
        $table->year('year');
        $table->timestamps();

        $table->unique(['user_id', 'category_id', 'month', 'year']);
    });
}
};
