<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | 1. LANGUAGE
        |--------------------------------------------------------------------------
        */

        $lang = $request->query('lang') === 'en'
            ? 'en'
            : 'bn';

        /*
        |--------------------------------------------------------------------------
        | 2. FIX UNDEFINED $preview
        |--------------------------------------------------------------------------
        |
        | Draft preview is allowed only for authenticated users
        | in the local development environment.
        |
        | Normal visitors see published news only.
        |--------------------------------------------------------------------------
        */

        $preview = app()->environment('local')
            && auth()->check()
            && $request->boolean('preview');

        /*
        |--------------------------------------------------------------------------
        | 3. ACTIVE CATEGORIES
        |--------------------------------------------------------------------------
        */

        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. NEWS QUERY
        |--------------------------------------------------------------------------
        */

        $query = NewsPost::query()
            ->with('category')
            ->whereHas('category', function ($q) {
                $q->where('is_active', true);
            });

        /*
        |--------------------------------------------------------------------------
        | 5. PUBLISHED NEWS FILTER
        |--------------------------------------------------------------------------
        */

        if (!$preview) {
            $query
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        /*
        |--------------------------------------------------------------------------
        | 6. OPTIONAL CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        $selectedCategory = trim(
            (string) $request->query('category', '')
        );

        if ($selectedCategory !== '') {
            $query->whereHas(
                'category',
                function ($q) use ($selectedCategory) {
                    $q->where('slug', $selectedCategory);
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. HOMEPAGE SEARCH
        |--------------------------------------------------------------------------
        */

        $search = trim(
            (string) $request->query('q', '')
        );

        $search = mb_substr($search, 0, 100);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('title_bn', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('excerpt_bn', 'like', "%{$search}%")
                    ->orWhere('excerpt_en', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 8. GET NEWS
        |--------------------------------------------------------------------------
        */

        $posts = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. HERO NEWS
        |--------------------------------------------------------------------------
        */

        $hero = $posts->first(
            fn ($post) => (bool) $post->is_featured
        ) ?: $posts->first();

        /*
        |--------------------------------------------------------------------------
        | 10. FOUR SUB STORIES
        |--------------------------------------------------------------------------
        */

        $subStories = $posts
            ->reject(fn ($post) => $post->id === $hero?->id)
            ->take(4)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 11. LATEST POSTS
        |--------------------------------------------------------------------------
        */

        $latestPosts = $posts
            ->take(7)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 12. MOST READ
        |--------------------------------------------------------------------------
        */

        $mostReadPosts = $posts
            ->sortByDesc('views')
            ->take(6)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 13. TRENDING POSTS
        |--------------------------------------------------------------------------
        |
        | NewsPost does not need an is_trending column.
        | Popularity is calculated using views.
        |--------------------------------------------------------------------------
        */

        $trendingPosts = $posts
            ->sortByDesc('views')
            ->take(4)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 14. BREAKING NEWS
        |--------------------------------------------------------------------------
        */

        $breakingPosts = $posts
            ->filter(fn ($post) => (bool) $post->is_breaking)
            ->take(5)
            ->values();

        if ($breakingPosts->isEmpty()) {
            $breakingPosts = $posts
                ->take(5)
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 15. CATEGORY SLUGS
        |--------------------------------------------------------------------------
        */

        $slugs = [
            'national',
            'chattogram',
            'international',
            'politics',
            'economy',
            'sports',
            'entertainment',
            'technology',
            'video',
            'investigation',
            'health',
            'education',
            'law-courts',
            'opinion',
            'interviews',
            'photo-gallery',
            'lifestyle',
            'journal',
        ];

        /*
        |--------------------------------------------------------------------------
        | 16. CATEGORY NEWS SECTIONS
        |--------------------------------------------------------------------------
        */

        $sections = [];

        foreach ($slugs as $slug) {
            $sections[$slug] = $posts
                ->filter(function ($post) use ($slug) {

                    $postSlug = $post->category?->slug;

                    if ($slug === 'technology') {
                        return in_array(
                            $postSlug,
                            ['technology', 'tech-ai'],
                            true
                        );
                    }

                    return $postSlug === $slug;

                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 17. SIX FEATURED CARDS
        |--------------------------------------------------------------------------
        */

        $featuredCards = $posts
            ->filter(function ($post) use ($hero) {

                return $post->id !== $hero?->id
                    && in_array(
                        $post->category?->slug,
                        [
                            'chattogram',
                            'politics',
                            'technology',
                            'tech-ai',
                            'national',
                        ],
                        true
                    );

            })
            ->take(6)
            ->values();

        if ($featuredCards->count() < 6) {

            $additional = $posts
                ->reject(function ($post) use (
                    $hero,
                    $featuredCards
                ) {

                    return $post->id === $hero?->id
                        || $featuredCards->contains(
                            'id',
                            $post->id
                        );

                })
                ->take(6 - $featuredCards->count());

            $featuredCards = $featuredCards
                ->concat($additional)
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 18. INVESTIGATION POSTS
        |--------------------------------------------------------------------------
        */

        $investigationPosts = $sections['investigation'];

        $investigationLead = $investigationPosts->first();

        $investigationOthers = $investigationPosts
            ->skip(1)
            ->take(6)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 19. DEEP REPORTS
        |--------------------------------------------------------------------------
        */

        $deepReports = $investigationPosts
            ->skip(1)
            ->take(6)
            ->values();

        if ($deepReports->count() < 6) {

            $additional = $posts
                ->reject(function ($post) use ($deepReports) {

                    return $deepReports->contains(
                        'id',
                        $post->id
                    );

                })
                ->take(6 - $deepReports->count());

            $deepReports = $deepReports
                ->concat($additional)
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | 20. ORIGINAL THREE-COLUMN CATEGORY STRUCTURE
        |--------------------------------------------------------------------------
        */

        $categoryColumns = [
            ['national', 'politics'],
            ['chattogram', 'international'],
            ['sports'],
        ];

        /*
        |--------------------------------------------------------------------------
        | 21. ORIGINAL FIVE-COLUMN CATEGORY GRID
        |--------------------------------------------------------------------------
        */

        $categoryGridSlugs = [
            'economy',
            'law-courts',
            'education',
            'opinion',
            'interviews',
        ];

        /*
        |--------------------------------------------------------------------------
        | 22. ORIGINAL RED CATEGORY SECTIONS
        |--------------------------------------------------------------------------
        */

        $redCategorySlugs = [
            'lifestyle',
            'entertainment',
            'technology',
            'journal',
        ];

        /*
        |--------------------------------------------------------------------------
        | 23. COMPATIBILITY WITH CURRENT HOMEPAGE BLADE
        |--------------------------------------------------------------------------
        |
        | These are the exact variable names used in your
        | latest resources/views/home/index.blade.php.
        |--------------------------------------------------------------------------
        */

        $threeColumns = $categoryColumns;

        $fiveCategories = $categoryGridSlugs;

        $redCategories = $redCategorySlugs;

        /*
        |--------------------------------------------------------------------------
        | 24. CATEGORY DISPLAY LABELS
        |--------------------------------------------------------------------------
        */

        $sectionLabels = [
            'national' => ['জাতীয়', 'National'],

            'chattogram' => ['চট্টগ্রাম', 'Chattogram'],

            'international' => ['আন্তর্জাতিক', 'International'],

            'politics' => ['রাজনীতি', 'Politics'],

            'economy' => ['অর্থ-বাণিজ্য', 'Economy & Trade'],

            'sports' => ['খেলাধুলা', 'Sports'],

            'entertainment' => ['বিনোদন', 'Entertainment'],

            'technology' => ['তথ্যপ্রযুক্তি', 'Information Technology'],

            'video' => ['ভিডিও', 'Video'],

            'investigation' => ['অনুসন্ধান সেল', 'Investigative Desk'],

            'health' => ['স্বাস্থ্য ও চিকিৎসা', 'Health & Wellness'],

            'education' => ['শিক্ষাঙ্গন', 'Education'],

            'law-courts' => ['আইন-আদালত', 'Law & Courts'],

            'opinion' => ['মুক্তচিন্তা', 'Opinion & Op-Ed'],

            'interviews' => ['সাক্ষাৎকার', 'Exclusive Interview'],

            'photo-gallery' => ['ছবিঘর', 'Photo Gallery'],

            'lifestyle' => ['লাইফস্টাইল', 'Lifestyle'],

            'journal' => [
                'ডেইলি দরিয়ানগর জার্নাল',
                'Daily Dariyanagar Journal'
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | 25. DYNAMIC NEWS TITLE
        |--------------------------------------------------------------------------
        */

        $title = static function ($post) use ($lang): string {

            if (!$post) {
                return '';
            }

            return (string) (
                $post->{'title_' . $lang}
                ?: $post->title_bn
                ?: $post->title_en
                ?: ''
            );

        };

        /*
        |--------------------------------------------------------------------------
        | 26. DYNAMIC NEWS DESCRIPTION
        |--------------------------------------------------------------------------
        */

        $summary = static function ($post) use ($lang): string {

            if (!$post) {
                return '';
            }

            $value = $post->{'excerpt_' . $lang}
                ?: $post->excerpt_bn
                ?: $post->excerpt_en;

            if (blank($value)) {

                $body = (string) (
                    $post->{'body_' . $lang}
                    ?: $post->body_bn
                    ?: $post->body_en
                    ?: ''
                );

                $value = strip_tags($body);
            }

            $value = html_entity_decode(
                (string) $value,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );

            $value = preg_replace(
                '/\s+/u',
                ' ',
                $value
            ) ?? $value;

            return Str::limit(
                trim($value),
                170
            );

        };

        /*
        |--------------------------------------------------------------------------
        | 27. DYNAMIC POST DETAILS URL
        |--------------------------------------------------------------------------
        |
        | Every post opens its own article URL.
        |
        | Example:
        | /news/example-news-slug
        |--------------------------------------------------------------------------
        */

        $postUrl = static function ($post) use (
            $lang,
            $preview
        ): string {

            if (!$post || blank($post->slug)) {
                return route('home');
            }

            return route(
                'news.show',
                array_merge(
                    [
                        'slug' => $post->slug,
                    ],
                    $lang === 'en'
                        ? ['lang' => 'en']
                        : [],
                    $preview
                        ? ['preview' => 1]
                        : []
                )
            );

        };

        /*
        |--------------------------------------------------------------------------
        | 28. DYNAMIC FEATURED IMAGE
        |--------------------------------------------------------------------------
        */

        $image = static function (
            $post,
            ?string $fallback = null
        ): string {

            $fallback ??= asset(
                'frontend/img/logo.png'
            );

            $path = trim(
                (string) ($post?->featured_image ?? '')
            );

            if ($path === '') {
                return $fallback;
            }

            if (
                Str::startsWith(
                    $path,
                    ['https://', 'http://']
                )
            ) {
                return $path;
            }

            if (
                Str::startsWith(
                    $path,
                    ['/storage/', 'storage/']
                )
            ) {
                return url(
                    '/' . ltrim($path, '/')
                );
            }

            return Storage::disk('public')->url(
                $path
            );

        };

        /*
        |--------------------------------------------------------------------------
        | 29. IMAGE HELPER - OLD NAME COMPATIBILITY
        |--------------------------------------------------------------------------
        */

        $imageUrl = static fn ($post): string =>
            $image($post);

        /*
        |--------------------------------------------------------------------------
        | 30. DYNAMIC SECTION HELPER
        |--------------------------------------------------------------------------
        */

        $section = static fn (string $slug) =>
            $sections[$slug] ?? collect();

        /*
        |--------------------------------------------------------------------------
        | 31. DYNAMIC CATEGORY LABEL HELPER
        |--------------------------------------------------------------------------
        */

        $label = static function (string $slug) use (
            $lang,
            $sectionLabels,
            $categories
        ): string {

            $category = $categories->firstWhere(
                'slug',
                $slug
            );

            if (
                !$category &&
                $slug === 'technology'
            ) {
                $category = $categories->firstWhere(
                    'slug',
                    'tech-ai'
                );
            }

            $translated = $category?->{'name_' . $lang};

            if (filled($translated)) {
                return (string) $translated;
            }

            return (string) (
                $sectionLabels[$slug][
                    $lang === 'bn' ? 0 : 1
                ] ?? $slug
            );

        };

        /*
        |--------------------------------------------------------------------------
        | 32. DYNAMIC CATEGORY NAVIGATION
        |--------------------------------------------------------------------------
        |
        | All navigation items now open /category/{slug}.
        |--------------------------------------------------------------------------
        */

        $navigation = $categories
            ->map(function ($category) use (
                $lang,
                $preview
            ) {

                return [
                    'bn' => $category->name_bn,

                    'en' => $category->name_en,

                    'url' => route(
                        'category.show',
                        array_merge(
                            [
                                'slug' => $category->slug,
                            ],
                            $lang === 'en'
                                ? ['lang' => 'en']
                                : [],
                            $preview
                                ? ['preview' => 1]
                                : []
                        )
                    ),
                ];

            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | 33. BREAKING NEWS TICKER
        |--------------------------------------------------------------------------
        */

        $tickerItems = $breakingPosts
            ->map(function ($post) use ($postUrl) {

                return [
                    'bn' => $post->title_bn
                        ?: $post->title_en,

                    'en' => $post->title_en
                        ?: $post->title_bn,

                    'url' => $postUrl($post),
                ];

            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | 34. TICKER VARIABLE FOR HOMEPAGE BLADE
        |--------------------------------------------------------------------------
        */

        $ticker = $tickerItems;

        /*
        |--------------------------------------------------------------------------
        | 35. HOMEPAGE SEO TITLE
        |--------------------------------------------------------------------------
        */

        $pageTitle = $lang === 'bn'
            ? 'ডেইলি দরিয়ানগর | সর্বশেষ সংবাদ'
            : 'Daily Dariyanagar | Latest News';

        if ($selectedCategory !== '') {

            $category = $categories->firstWhere(
                'slug',
                $selectedCategory
            );

            if ($category) {

                $pageTitle = (
                    $category->{'name_' . $lang}
                    ?: $category->name_bn
                ) . ' | Daily Dariyanagar';

            }

        }

        if ($search !== '') {

            $pageTitle = (
                $lang === 'bn'
                    ? 'অনুসন্ধান: '
                    : 'Search: '
            )
                . $search
                . ' | Daily Dariyanagar';

        }

        /*
        |--------------------------------------------------------------------------
        | 36. RETURN DYNAMIC HOMEPAGE
        |--------------------------------------------------------------------------
        |
        | All variables expected by the homepage Blade
        | are now included.
        |--------------------------------------------------------------------------
        */

        return view(
            'home.index',
            compact(
                'lang',
                'preview',
                'categories',
                'posts',
                'hero',
                'subStories',
                'latestPosts',
                'mostReadPosts',
                'trendingPosts',
                'breakingPosts',
                'sections',
                'featuredCards',
                'deepReports',
                'investigationLead',
                'investigationOthers',
                'categoryColumns',
                'categoryGridSlugs',
                'redCategorySlugs',
                'threeColumns',
                'fiveCategories',
                'redCategories',
                'sectionLabels',
                'title',
                'summary',
                'postUrl',
                'image',
                'imageUrl',
                'section',
                'label',
                'navigation',
                'tickerItems',
                'ticker',
                'pageTitle',
                'selectedCategory',
                'search'
            )
        );
    }
}
