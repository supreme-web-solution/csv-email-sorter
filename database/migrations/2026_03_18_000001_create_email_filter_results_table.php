<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_filter_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('filename');
            $table->string('token')->unique();
            $table->unsignedInteger('source_count');
            $table->unsignedInteger('exclude_count');
            $table->unsignedInteger('result_count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_filter_results');
    }
};
