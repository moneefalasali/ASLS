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
        Schema::create('sign_assets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index()->comment('letter or word key');
            $table->string('language')->default('en');
            $table->enum('type', ['image', 'animation'])->default('image');
            $table->string('src');
            $table->string('text')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign_assets');
    }
};
