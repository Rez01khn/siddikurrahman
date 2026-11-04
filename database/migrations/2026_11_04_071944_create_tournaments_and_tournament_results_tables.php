<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('tour')->nullable();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('official_url', 2048)->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('status')->default('upcoming');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->timestamps();
            $table->index(['is_published', 'status', 'start_date']);
            $table->index('sort_order');
        });

        Schema::create('tournament_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tournament_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('final_position', 30)->nullable();
            $table->unsignedSmallInteger('total_score')->nullable();
            $table->smallInteger('score_to_par')->nullable();
            foreach ([1, 2, 3, 4] as $round) {
                $table->unsignedSmallInteger('round_'.$round)->nullable();
            }
            $table->decimal('prize_amount', 14, 2)->nullable();
            $table->char('prize_currency', 3)->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_results');
        Schema::dropIfExists('tournaments');
    }
};
