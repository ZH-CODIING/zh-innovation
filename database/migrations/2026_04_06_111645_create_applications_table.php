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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

        $table->foreignId('job_id')
              ->constrained('job_posts') 
              ->onDelete('cascade');

            $table->string('full_name'); // الاسم الكامل
            $table->string('phone'); // رقم الهاتف
            $table->string('whatsapp')->nullable(); // واتساب اختياري

            $table->string('resume'); // ملف السيرة الذاتية
            $table->text('cover_letter')->nullable(); // رسالة التغطية
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};