<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $t): void {
            $t->id();
            $t->string('name', 120);
            $t->string('email', 254);
            $t->string('subject', 200);
            $t->text('message');
            $t->timestamp('read_at')->nullable()->index();
            $t->timestamps();
        });
        Schema::create('website_settings', function (Blueprint $t): void {
            $t->id();
            $t->string('site_name')->nullable();
            $t->string('logo')->nullable();
            $t->string('favicon')->nullable();
            $t->string('contact_email', 254)->nullable();
            $t->string('contact_phone', 60)->nullable();
            $t->string('contact_address', 500)->nullable();
            $t->text('footer_text')->nullable();
            $t->string('seo_title', 160)->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->json('social_links')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
        Schema::dropIfExists('contact_messages');
    }
};
