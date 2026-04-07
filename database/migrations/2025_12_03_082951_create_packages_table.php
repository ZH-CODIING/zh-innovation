<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('packages', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2)->default(0);
        $table->json('features')->nullable(); // مميزات الباقة
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('packages');
}

};
