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
        Schema::table('promotionals', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('description')->after('title');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
            $table->datetime('expires_at')->nullable()->after('status');
            $table->string('image')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotionals', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'status', 'expires_at', 'image']);
        });
    }
};
