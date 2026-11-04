<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legacy_import_batches', function (Blueprint $t): void {
            $t->id();
            $t->string('dataset_key')->unique();
            $t->char('source_sha256', 64)->unique();
            $t->string('original_filename');
            $t->string('archive_path');
            $t->json('summary')->nullable();
            $t->timestamps();
        });
        Schema::create('legacy_import_entries', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('legacy_import_batch_id')->constrained()->restrictOnDelete();
            $t->string('source_key', 180);
            $t->string('section', 80);
            $t->string('state', 30);
            $t->json('raw_data');
            $t->string('target_table')->nullable();
            $t->unsignedBigInteger('target_id')->nullable();
            $t->text('review_note')->nullable();
            $t->timestamps();
            $t->unique(['legacy_import_batch_id', 'source_key']);
            $t->index(['section', 'state']);
        });
        Schema::table('tournaments', function (Blueprint $t): void {
            $t->decimal('tournament_purse_amount', 16, 2)->nullable();
            $t->char('tournament_purse_currency', 3)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_import_entries');
        Schema::dropIfExists('legacy_import_batches');
        Schema::table('tournaments', function (Blueprint $t): void {
            $t->dropColumn(['tournament_purse_amount', 'tournament_purse_currency']);
        });
    }
};
