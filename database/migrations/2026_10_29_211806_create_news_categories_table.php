
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();

            // Bilingual category information
            $table->string('name_bn');
            $table->string('name_en');
            $table->string('slug')->unique();

            $table->text('description_bn')->nullable();
            $table->text('description_en')->nullable();

            // Category image
            $table->string('image')->nullable();
            $table->string('image_alt_bn')->nullable();
            $table->string('image_alt_en')->nullable();

            // Search engine metadata
            $table->string('seo_title_bn')->nullable();
            $table->string('seo_title_en')->nullable();

            $table->text('seo_description_bn')->nullable();
            $table->text('seo_description_en')->nullable();

            $table->string('focus_keyword_bn')->nullable();
            $table->string('focus_keyword_en')->nullable();

            $table->string('canonical_url', 2048)->nullable();

            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);

            // Open Graph
            $table->string('og_title_bn')->nullable();
            $table->string('og_title_en')->nullable();
            $table->text('og_description_bn')->nullable();
            $table->text('og_description_en')->nullable();
            $table->string('og_image')->nullable();

            // Twitter / X
            $table->string('twitter_title_bn')->nullable();
            $table->string('twitter_title_en')->nullable();
            $table->text('twitter_description_bn')->nullable();
            $table->text('twitter_description_en')->nullable();

            // Category settings
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_categories');
    }
};
