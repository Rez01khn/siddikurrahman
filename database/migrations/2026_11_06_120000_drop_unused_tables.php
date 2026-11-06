<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('social_work_images');
        Schema::dropIfExists('social_works');
        Schema::dropIfExists('tournament_results');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('legacy_import_entries');
        Schema::dropIfExists('legacy_import_batches');
        Schema::dropIfExists('equipment');
    }

    public function down(): void
    {
        // No down migration needed as these features are permanently removed.
    }
};
