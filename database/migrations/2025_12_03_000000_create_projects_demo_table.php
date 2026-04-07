<?php
// Migration: database/migrations/2025_12_03_000000_create_projects_demo_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up()
{
Schema::create('projects_demo', function (Blueprint $table) {
$table->id();
$table->string('name');
$table->text('description')->nullable();
$table->json('technologies')->nullable(); // array of strings
$table->json('images')->nullable(); // array of image paths (multiple)
$table->string('cover_image')->nullable();
$table->string('demo_link')->nullable();
$table->string('type')->nullable();
$table->timestamps();
});
}


public function down()
{
Schema::dropIfExists('projects_demo');
}
};