<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('bookmark_categories')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('url');
            $table->string('url_hash', 64);
            $table->string('folder_path')->nullable();
            $table->string('browser')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();

            $table->unique(['user_id', 'url_hash']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
