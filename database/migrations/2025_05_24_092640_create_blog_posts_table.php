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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // العنوان
            $table->text('description'); // التفاصيل
            $table->string('image')->nullable(); // صورة الكافر
            $table->string('video_url')->nullable();
            
            // صور لكل نص: نستخدم json لتخزين مصفوفة روابط الصور
            $table->json('content_images')->nullable(); 

            // آيدي الكاتب: يربط بجدول المستخدمين ويحذف المقالات إذا حذف المستخدم
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};