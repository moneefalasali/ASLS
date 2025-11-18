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
        Schema::table('sign_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('sign_assets', 'category')) {
                $table->string('category')->nullable()->after('active');
            }
            if (!Schema::hasColumn('sign_assets', 'difficulty_level')) {
                $table->string('difficulty_level')->nullable()->after('category');
            }
            if (!Schema::hasColumn('sign_assets', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('difficulty_level');
            }
            if (!Schema::hasColumn('sign_assets', 'description')) {
                $table->text('description')->nullable()->after('usage_count');
            }
            if (!Schema::hasColumn('sign_assets', 'tags')) {
                // JSON column for tags (nullable)
                $table->json('tags')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sign_assets', function (Blueprint $table) {
            if (Schema::hasColumn('sign_assets', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('sign_assets', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('sign_assets', 'usage_count')) {
                $table->dropColumn('usage_count');
            }
            if (Schema::hasColumn('sign_assets', 'difficulty_level')) {
                $table->dropColumn('difficulty_level');
            }
            if (Schema::hasColumn('sign_assets', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
