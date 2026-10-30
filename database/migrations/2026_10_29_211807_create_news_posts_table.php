
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('news_category_id')
                ->constrained('news_categories')
                ->restrictOnDelete();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Bilingual article information
            $table->string('title_bn');
            $table->string('title_en');
            $table->string('slug')->unique();

            $table->text('excerpt_bn')->nullable();
            $table->text('excerpt_en')->nullable();

            $table->longText('body_bn');
            $table->longText('body_en');

            // Byline and source
            $table->string('reporter_bn')->nullable();
            $table->string('reporter_en')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url', 2048)->nullable();

            // Featured image
            $table->string('featured_image')->nullable();
            $table->string('image_alt_bn')->nullable();
            $table->string('image_alt_en')->nullable();
            $table->string('image_caption_bn')->nullable();
            $table->string('image_caption_en')->nullable();
            $table->string('image_credit')->nullable();

            // Publishing settings
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);

            $table->unsignedBigInteger('views')->default(0);

            // Search engine SEO
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

            // Structured data type
            $table->string('schema_type', 30)
                ->default('NewsArticle');

            $table->timestamps();

            $table->index([
                'status',
                'published_at',
            ]);

            $table->index([
                'news_category_id',
                'published_at',
            ]);

            $table->index('is_breaking');
            $table->index('is_featured');
            $table->index('is_trending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_posts');
    }
};
