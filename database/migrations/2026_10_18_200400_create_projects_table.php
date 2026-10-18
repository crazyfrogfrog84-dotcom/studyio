<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('project_url', 2048)->nullable();
            $table->string('screenshot_path', 1024)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
