<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biographies', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('full_name');
            $table->string('professional_title');
            $table->text('short_introduction')->nullable();
            $table->longText('full_biography')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('nationality')->nullable();
            $table->unsignedSmallInteger('professional_start_year')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('profile_image_alt')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->json('additional_details')->nullable();
            $table->string('seo_title', 160)->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->text('source_note')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        foreach (['career_milestones', 'achievements'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name): void {
                $table->id();
                $table->string('title');
                $table->unsignedSmallInteger('year')->nullable();
                $table->date('event_date')->nullable();
                $table->text('description');
                $table->string('image')->nullable();
                $table->string('image_alt')->nullable();
                $table->text('source_note')->nullable();
                if ($name === 'achievements') {
                    $table->string('category');
                    $table->string('event_name')->nullable();
                    $table->boolean('is_featured')->default(false);
                }
                $table->boolean('is_published')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['is_published', 'sort_order']);
            });
        }

        Schema::create('career_statistics', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('value', 100);
            $table->text('description')->nullable();
            $table->text('source_note');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_statistics');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('career_milestones');
        Schema::dropIfExists('biographies');
    }
};
