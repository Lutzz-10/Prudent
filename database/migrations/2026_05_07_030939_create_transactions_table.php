<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_create_transactions_table.php
public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('receipt_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        $table->decimal('total_amount', 15, 2);
        $table->string('notes')->nullable();
        $table->date('transaction_date');
        $table->timestamps();
    });
}
};
