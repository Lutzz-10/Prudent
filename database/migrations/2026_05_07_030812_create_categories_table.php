<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/xxxx_create_categories_table.php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('icon');        // emoji atau nama icon
        $table->string('color', 7);    // hex color, e.g. #F59E0B
        $table->boolean('is_default')->default(true);
        $table->timestamps();
    });
}
};
