<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_info', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('cv')->nullable(); // Path to PDF
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('job_title')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('hero_section_image')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('footer_description')->nullable();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_info');
    }
};
