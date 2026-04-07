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
        Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->text('comment_text');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // صاحب التعليق
    $table->foreignId('blog_post_id')->constrained()->onDelete('cascade'); // المقال المرتبط
    $table->unsignedBigInteger('parent_id')->nullable(); // للردود: يشير إلى id التعليق الأب
    $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
