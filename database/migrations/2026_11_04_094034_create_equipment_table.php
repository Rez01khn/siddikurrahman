<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['equipment', 'coaching_tips'] as $table) {
            Schema::create($table, function (Blueprint $t) use ($table): void {
                $t->id();
                $t->string('title');
                $t->string('slug', 180)->unique();
                $t->string('category', 100)->nullable();
                $t->text('excerpt')->nullable();
                $t->longText('description')->nullable();
                $t->string('image')->nullable();
                $t->string('image_alt')->nullable();
                $t->string('source_url', 2048)->nullable();
                if ($table === 'equipment') {
                    $t->string('brand', 100)->nullable();
                    $t->string('model_name')->nullable();
                }
                $t->boolean('is_published')->default(false);
                $t->boolean('is_featured')->default(false);
                $t->unsignedInteger('sort_order')->default(0);
                $t->string('seo_title', 160)->nullable();
                $t->string('seo_description', 320)->nullable();
                $t->timestamps();
                $t->index(['is_published', 'is_featured', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_tips');
        Schema::dropIfExists('equipment');
    }
};
