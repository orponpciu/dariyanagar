<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class NewsPost extends Model
{
    protected $fillable = [
        'news_category_id',
        'author_id',
        'title_bn',
        'title_en',
        'slug',
        'excerpt_bn',
        'excerpt_en',
        'body_bn',
        'body_en',
        'reporter_bn',
        'reporter_en',
        'source_name',
        'source_url',
        'featured_image',
        'image_alt_bn',
        'image_alt_en',
        'image_caption_bn',
        'image_caption_en',
        'image_credit',
        'status',
        'published_at',
        'is_breaking',
        'is_featured',
        'is_trending',
        'views',
        'seo_title_bn',
        'seo_title_en',
        'seo_description_bn',
        'seo_description_en',
        'focus_keyword_bn',
        'focus_keyword_en',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'og_title_bn',
        'og_title_en',
        'og_description_bn',
        'og_description_en',
        'og_image',
        'twitter_title_bn',
        'twitter_title_en',
        'twitter_description_bn',
        'twitter_description_en',
        'schema_type',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'views' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (NewsPost $post): void {
            if (empty($post->author_id) && auth()->check()) {
                $post->author_id = auth()->id();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            NewsCategory::class,
            'news_category_id'
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
