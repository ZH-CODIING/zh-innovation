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
      Schema::create('links', function (Blueprint $table) {
    $table->id();
    $table->string('title')->nullable(); // اسم اختياري للرابط
    $table->string('url'); // اللينك نفسه
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
