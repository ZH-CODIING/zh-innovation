<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects_demo')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->string('buyer_name');
            $table->string('buyer_email')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
