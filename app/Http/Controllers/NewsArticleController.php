<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsArticleController extends Controller
{
    /**
     * Display a published news article.
     *
     * Bengali:
     * /news/article-slug
     *
     * English:
     * /news/article-slug?lang=en
     */
    public function show(Request $request, string $slug): View
    {
        /*
        |--------------------------------------------------------------------------
        | 1. FETCH PUBLISHED NEWS ARTICLE
        |--------------------------------------------------------------------------
        */

        $post = NewsPost::query()
            ->with([
                'category',
                'author',
            ])
            ->published()
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->where('slug', $slug)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | 2. DETECT CURRENT LANGUAGE
        |--------------------------------------------------------------------------
        */

        $lang = $request->query('lang') === 'en'
            ? 'en'
            : 'bn';


        /*
        |--------------------------------------------------------------------------
        | 3. DYNAMIC ARTICLE HEADLINE
        |--------------------------------------------------------------------------
        */

        $headline = $post->{'title_' . $lang};


        /*
        |--------------------------------------------------------------------------
        | 4. ARTICLE REPORTER / AUTHOR
        |--------------------------------------------------------------------------
        */

        $reporterName = $post->{'reporter_' . $lang};

        $hasPersonAuthor = filled($reporterName)
            || filled($post->author?->name);

        $reporter = $reporterName
            ?: $post->author?->name
            ?: 'Daily Dariyanagar News Desk';


        /*
        |--------------------------------------------------------------------------
        | 5. ARTICLE VIEW COUNTING
        |--------------------------------------------------------------------------
        |
        | Counts one view per session every 30 minutes.
        | This is lightweight session-based counting.
        |--------------------------------------------------------------------------
        */

        $sessionKey = 'news_article_viewed_at_' . $post->getKey();

        $lastViewed = (int) $request->session()->get(
            $sessionKey,
            0
        );

        if (
            $lastViewed === 0
            || $lastViewed <= now()->subMinutes(30)->timestamp
        ) {
            $post->increment('views');

            $request->session()->put(
                $sessionKey,
                now()->timestamp
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 6. FETCH ACTIVE NEWS CATEGORIES
        |--------------------------------------------------------------------------
        */

        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name_en', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 7. BASE QUERY FOR PUBLICLY AVAILABLE NEWS
        |--------------------------------------------------------------------------
        |
        | Only published articles from active categories.
        |--------------------------------------------------------------------------
        */

        $newsQuery = function () {
            return NewsPost::query()
                ->published()
                ->whereHas('category', function ($query) {
                    $query->where('is_active', true);
                });
        };


        /*
        |--------------------------------------------------------------------------
        | 8. LATEST NEWS SIDEBAR
        |--------------------------------------------------------------------------
        */

        $latestPosts = $newsQuery()
            ->where('id', '!=', $post->getKey())
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 9. MOST READ NEWS SIDEBAR
        |--------------------------------------------------------------------------
        */

        $mostReadPosts = $newsQuery()
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 10. RELATED NEWS ARTICLES
        |--------------------------------------------------------------------------
        |
        | First select articles from the same category.
        | If fewer than 6 exist, fill with other latest news.
        |--------------------------------------------------------------------------
        */

        $relatedPosts = $newsQuery()
            ->where('id', '!=', $post->getKey())
            ->where(
                'news_category_id',
                $post->news_category_id
            )
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        if ($relatedPosts->count() < 6) {

            $remainingCount = 6 - $relatedPosts->count();

            $excludedIds = $relatedPosts
                ->pluck('id')
                ->push($post->getKey())
                ->all();

            $additionalPosts = $newsQuery()
                ->whereNotIn('id', $excludedIds)
                ->orderByDesc('published_at')
                ->limit($remainingCount)
                ->get();

            $relatedPosts = $relatedPosts->concat(
                $additionalPosts
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 11. BREAKING NEWS TICKER
        |--------------------------------------------------------------------------
        */

        $breakingPosts = $newsQuery()
            ->where('is_breaking', true)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        // Use latest articles if no breaking news exists.
        if ($breakingPosts->isEmpty()) {
            $breakingPosts = $newsQuery()
                ->orderByDesc('published_at')
                ->limit(5)
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | 12. ARTICLE URLS
        |--------------------------------------------------------------------------
        */

        $articleUrl = route('news.show', [
            'slug' => $post->slug,
        ]);

        $bengaliUrl = $articleUrl;

        $englishUrl = $articleUrl . '?lang=en';


        /*
        |--------------------------------------------------------------------------
        | 13. DYNAMIC SEO TITLE
        |--------------------------------------------------------------------------
        */

        $seoTitle = $post->{'seo_title_' . $lang}
            ?: $headline . (
                $lang === 'bn'
                    ? ' | ডেইলি দরিয়ানগর'
                    : ' | Daily Dariyanagar'
            );


        /*
        |--------------------------------------------------------------------------
        | 14. DYNAMIC META DESCRIPTION
        |--------------------------------------------------------------------------
        */

        $seoDescription = $post->{'seo_description_' . $lang}
            ?: $post->{'excerpt_' . $lang}
            ?: Str::limit(
                strip_tags(
                    $post->{'body_' . $lang} ?? ''
                ),
                160
            );

        $seoDescription = trim(
            preg_replace('/\s+/u', ' ', $seoDescription)
            ?? $seoDescription
        );


        /*
        |--------------------------------------------------------------------------
        | 15. FOCUS KEYWORD
        |--------------------------------------------------------------------------
        */

        $focusKeyword = $post->{'focus_keyword_' . $lang};


        /*
        |--------------------------------------------------------------------------
        | 16. CANONICAL URL
        |--------------------------------------------------------------------------
        |
        | Uses language-specific URLs by default.
        | An optional canonical override applies to Bengali.
        |--------------------------------------------------------------------------
        */

        $canonicalOverride = filled($post->canonical_url)
            && filter_var(
                $post->canonical_url,
                FILTER_VALIDATE_URL
            );

        $canonical = $lang === 'en'
            ? $englishUrl
            : (
                $canonicalOverride
                    ? $post->canonical_url
                    : $bengaliUrl
            );


        /*
        |--------------------------------------------------------------------------
        | 17. SEARCH ENGINE ROBOTS DIRECTIVES
        |--------------------------------------------------------------------------
        */

        $indexDirective = $post->robots_index
            ? 'index'
            : 'noindex';

        $followDirective = $post->robots_follow
            ? 'follow'
            : 'nofollow';

        $robots = implode(', ', [
            $indexDirective,
            $followDirective,
            'max-image-preview:large',
            'max-snippet:-1',
            'max-video-preview:-1',
        ]);


        /*
        |--------------------------------------------------------------------------
        | 18. OPEN GRAPH METADATA
        |--------------------------------------------------------------------------
        */

        $ogTitle = $post->{'og_title_' . $lang}
            ?: $seoTitle;

        $ogDescription = $post->{'og_description_' . $lang}
            ?: $seoDescription;


        /*
        |--------------------------------------------------------------------------
        | 19. TWITTER / X METADATA
        |--------------------------------------------------------------------------
        */

        $twitterTitle = $post->{'twitter_title_' . $lang}
            ?: $ogTitle;

        $twitterDescription = $post->{'twitter_description_' . $lang}
            ?: $ogDescription;


        /*
        |--------------------------------------------------------------------------
        | 20. PUBLIC IMAGE URL HELPER
        |--------------------------------------------------------------------------
        */

        $publicImage = fn (?string $path): ?string =>
            $this->publicImageUrl($path);


        /*
        |--------------------------------------------------------------------------
        | 21. FEATURED NEWS IMAGE
        |--------------------------------------------------------------------------
        */

        $featuredImageUrl = $publicImage(
            $post->featured_image
        );


        /*
        |--------------------------------------------------------------------------
        | 22. SOCIAL SHARING IMAGE
        |--------------------------------------------------------------------------
        */

        $ogImageUrl = $publicImage($post->og_image)
            ?: $featuredImageUrl
            ?: asset('frontend/img/logo.png');


        /*
        |--------------------------------------------------------------------------
        | 23. IMAGE CAPTION AND CREDIT
        |--------------------------------------------------------------------------
        */

        $imageCaption = $post->{'image_caption_' . $lang};

        if (filled($post->image_credit)) {

            $imageCaption = trim(
                (
                    filled($imageCaption)
                        ? $imageCaption . ' — '
                        : ''
                ) . $post->image_credit
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 24. RENDER FILAMENT 5 RICH TEXT CONTENT
        |--------------------------------------------------------------------------
        |
        | Matches the public disk used in NewsPostForm.
        | Filament handles editor image URLs and HTML
        | sanitization through RichContentRenderer.
        |--------------------------------------------------------------------------
        */

        $articleContent = $post->{'body_' . $lang} ?? '';

        $articleHtml = RichContentRenderer::make(
            $articleContent
        )
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public')
            ->toHtml();


        /*
        |--------------------------------------------------------------------------
        | 25. SPLIT ARTICLE CONTENT FOR ORIGINAL AD POSITIONS
        |--------------------------------------------------------------------------
        |
        | Part 0: Article content before first ad.
        | Part 1: Content between first and second ad.
        | Part 2: Remaining content after second ad.
        |
        | This does not change advertisement HTML or CSS.
        |--------------------------------------------------------------------------
        */

        $contentSegments = preg_split(
            '/(?<=<\/p>)/i',
            $articleHtml,
            3,
            PREG_SPLIT_NO_EMPTY
        );

        $contentParts = array_pad(
            $contentSegments ?: [],
            3,
            ''
        );


        /*
        |--------------------------------------------------------------------------
        | 26. PUBLICATION DATE LABEL
        |--------------------------------------------------------------------------
        */

        $publishedLabel = $post->published_at
            ? $post->published_at
                ->copy()
                ->locale($lang)
                ->translatedFormat('d F Y, h:i A')
            : '';


        /*
        |--------------------------------------------------------------------------
        | 27. STRUCTURED DATA TYPE
        |--------------------------------------------------------------------------
        */

        $schemaType = in_array(
            $post->schema_type,
            [
                'Article',
                'NewsArticle',
            ],
            true
        )
            ? $post->schema_type
            : 'NewsArticle';


        /*
        |--------------------------------------------------------------------------
        | 28. NEWSARTICLE JSON-LD STRUCTURED DATA
        |--------------------------------------------------------------------------
        */

        $structuredData = [

            '@context' => 'https://schema.org',

            '@type' => $schemaType,

            'headline' => $headline,

            'description' => $seoDescription,

            'inLanguage' => $lang === 'bn'
                ? 'bn-BD'
                : 'en-US',

            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $lang === 'en'
                    ? $englishUrl
                    : $articleUrl,
            ],

            'datePublished' => $post->published_at
                ?->toIso8601String(),

            'dateModified' => $post->updated_at
                ?->toIso8601String(),

            'author' => [
                '@type' => $hasPersonAuthor
                    ? 'Person'
                    : 'Organization',

                'name' => $reporter,
            ],

            'publisher' => [
                '@type' => 'NewsMediaOrganization',

                'name' => 'Daily Dariyanagar',

                'url' => url('/'),

                'logo' => [
                    '@type' => 'ImageObject',

                    'url' => asset(
                        'frontend/img/logo.png'
                    ),
                ],
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | 29. ADD ARTICLE FEATURED IMAGE TO STRUCTURED DATA
        |--------------------------------------------------------------------------
        */

        if ($featuredImageUrl) {
            $structuredData['image'] = [
                $featuredImageUrl,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 30. RETURN EXISTING BLADE VIEW
        |--------------------------------------------------------------------------
        |
        | Your actual Blade location:
        | resources/views/home/post-details.blade.php
        |--------------------------------------------------------------------------
        */

        return view('home.post-details', compact(
            'post',
            'lang',
            'headline',
            'reporter',
            'categories',
            'latestPosts',
            'mostReadPosts',
            'relatedPosts',
            'breakingPosts',
            'articleUrl',
            'bengaliUrl',
            'englishUrl',
            'seoTitle',
            'seoDescription',
            'focusKeyword',
            'canonical',
            'robots',
            'ogTitle',
            'ogDescription',
            'twitterTitle',
            'twitterDescription',
            'featuredImageUrl',
            'ogImageUrl',
            'imageCaption',
            'publicImage',
            'contentParts',
            'publishedLabel',
            'structuredData'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | 31. GENERATE PUBLIC IMAGE URL
    |--------------------------------------------------------------------------
    |
    | Converts Filament uploaded image paths into
    | publicly accessible Laravel storage URLs.
    |--------------------------------------------------------------------------
    */

    private function publicImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $url = Storage::disk('public')->url($path);

        if (
            Str::startsWith(
                $url,
                [
                    'https://',
                    'http://',
                ]
            )
        ) {
            return $url;
        }

        return url($url);
    }
}
