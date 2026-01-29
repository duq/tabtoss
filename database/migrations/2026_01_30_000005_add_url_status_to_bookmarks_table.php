<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->unsignedSmallInteger('url_status')->nullable()->after('ai_label');
            $table->timestamp('url_checked_at')->nullable()->after('url_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropColumn(['url_status', 'url_checked_at']);
        });
    }
};
