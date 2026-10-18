<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 160);
            $table->string('profile_picture_path', 1024)->nullable();
            $table->string('email');
            $table->string('contact_number', 60)->nullable();
            $table->text('address')->nullable();
            $table->text('about_me')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
