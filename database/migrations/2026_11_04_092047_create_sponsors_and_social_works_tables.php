<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsors', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('slug', 180)->unique();
            $t->string('logo')->nullable();
            $t->string('website_url', 2048)->nullable();
            $t->string('category', 100)->nullable();
            $t->text('description')->nullable();
            $t->date('partnership_start_date')->nullable();
            $t->date('partnership_end_date')->nullable();
            $t->string('source_url', 2048)->nullable();
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_published')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->index(['is_published', 'is_featured', 'sort_order']);
        });
        Schema::create('social_works', function (Blueprint $t): void {
            $t->id();
            $t->string('title');
            $t->string('slug', 180)->unique();
            $t->text('short_description')->nullable();
            $t->longText('full_description')->nullable();
            $t->date('activity_date')->nullable();
            $t->string('location')->nullable();
            $t->string('image')->nullable();
            $t->string('source_url', 2048)->nullable();
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_published')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->string('seo_title', 160)->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->timestamps();
            $t->index(['is_published', 'is_featured', 'sort_order']);
        });
        Schema::create('social_work_images', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('social_work_id')->constrained()->cascadeOnDelete();
            $t->string('image');
            $t->text('caption')->nullable();
            $t->string('alt_text');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_work_images');
        Schema::dropIfExists('social_works');
        Schema::dropIfExists('sponsors');
    }
};
