<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    protected $fillable = [
        'name_bn',
        'name_en',
        'slug',
        'description_bn',
        'description_en',
        'image',
        'image_alt_bn',
        'image_alt_en',
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
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(
            NewsPost::class,
            'news_category_id'
        );
    }
}
