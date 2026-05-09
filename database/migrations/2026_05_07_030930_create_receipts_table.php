<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_create_receipts_table.php
public function up(): void
{
    Schema::create('receipts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('image_path');
        $table->string('store_name')->nullable();
        $table->date('receipt_date')->nullable();
        $table->longText('raw_text')->nullable(); // hasil mentah OCR
        $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
        $table->timestamps();
    });
}
};
