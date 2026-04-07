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
    Schema::create('job_posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->string('location')->nullable();
        $table->string('salary')->nullable();
        $table->string('type');
        // يفضل استخدام foreignId للربط الصحيح
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
        $table->enum('status', ['open', 'filled', 'closed'])->default('open');
        $table->timestamps();
    });
}

public function down(): void
{
    // تم تغيير 'jobs' إلى 'job_posts' ليتطابق مع الـ up
    Schema::dropIfExists('job_posts');
}
};
