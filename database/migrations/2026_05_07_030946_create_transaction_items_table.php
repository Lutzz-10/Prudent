<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // database/migrations/xxxx_create_transaction_items_table.php
public function up(): void
{
    Schema::create('transaction_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
        $table->string('item_name');
        $table->integer('qty')->default(1);
        $table->decimal('unit_price', 15, 2);
        $table->decimal('subtotal', 15, 2);
        $table->timestamps();
    });
}
};
