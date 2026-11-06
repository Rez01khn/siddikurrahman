<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table): void {
            $table->string('category')->nullable()->default('Career Milestone')->change();
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table): void {
            $table->string('category')->nullable(false)->change();
        });
    }
};
