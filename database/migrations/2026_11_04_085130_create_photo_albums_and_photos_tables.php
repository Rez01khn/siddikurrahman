<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_albums', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->date('album_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('photo_album_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->text('caption')->nullable();
            $table->string('alt_text');
            $table->string('photographer_credit')->nullable();
            $table->date('photo_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_published', 'sort_order']);
        });
        Schema::create('videos', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('video_url', 2048);
            $table->string('image')->nullable();
            $table->date('video_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('photo_albums');
    }
};
