<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $lang = $request->query('lang') === 'en' ? 'en' : 'bn';

        // Draft previews are limited to authenticated users in local development.
        $preview = app()->environment('local')
            && auth()->check()
            && $request->boolean('preview');

        $category = NewsCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $categories = NewsCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $basePosts = NewsPost::query()
            ->with('category')
            ->where('news_category_id', $category->id);

        if (! $preview) {
            $basePosts->published();
        }

        $sort = in_array(
            $request->query('sort'),
            ['latest', 'popular', 'oldest'],
            true
        )
            ? $request->query('sort')
            : 'latest';

        $sortedPosts = clone $basePosts;

        if ($sort === 'popular') {
            $sortedPosts
                ->orderByDesc('views')
                ->orderByDesc('published_at')
                ->orderByDesc('id');

        } elseif ($sort === 'oldest') {
            $sortedPosts
                ->orderBy('published_at')
                ->orderBy('id');

        } elseif ($preview) {
            $sortedPosts
                ->orderByDesc('created_at')
                ->orderByDesc('id');

        } else {
            $sortedPosts
                ->orderByDesc('published_at')
                ->orderByDesc('id');
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination: 12 posts per page
        |--------------------------------------------------------------------------
        */

        $posts = $sortedPosts
            ->paginate(12)
            ->withQueryString();

        $lead = $posts->currentPage() === 1
            ? $posts->first()
            : null;

        $listPosts = $posts
            ->getCollection()
            ->when(
                $lead,
                fn ($collection) => $collection->skip(1)
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Sidebar posts use the same visibility as the category.
        |--------------------------------------------------------------------------
        */

        $sidebarQuery = fn () => $preview
            ? NewsPost::query()->whereHas(
                'category',
                fn ($q) => $q->where('is_active', true)
            )
            : NewsPost::query()->published();

        $latestPosts = $sidebarQuery()
            ->orderByDesc(
                $preview ? 'created_at' : 'published_at'
            )
            ->limit(6)
            ->get();

        $mostReadPosts = $sidebarQuery()
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $breakingPosts = $sidebarQuery()
            ->where('is_breaking', true)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        if ($breakingPosts->isEmpty()) {
            $breakingPosts = $latestPosts->take(5);
        }

        /*
        |--------------------------------------------------------------------------
        | Category and post URLs
        |--------------------------------------------------------------------------
        */

        $params = array_filter([
            'lang' => $lang === 'en' ? 'en' : null,
            'preview' => $preview ? 1 : null,
        ], fn ($value) => $value !== null);

        $categoryUrl = fn (NewsCategory $item) => route(
            'category.show',
            array_merge(['slug' => $item->slug], $params)
        );

        $postUrl = fn (NewsPost $item) => route(
            'news.show',
            array_merge(['slug' => $item->slug], $params)
        );

        /*
        |--------------------------------------------------------------------------
        | Bengali and English post fields
        |--------------------------------------------------------------------------
        */

        $title = fn (?NewsPost $item) =>
            $item?->{'title_' . $lang}
            ?: $item?->title_bn
            ?: '';

        $summary = fn (?NewsPost $item) =>
            $item?->{'excerpt_' . $lang}
            ?: Str::limit(
                strip_tags($item?->{'body_' . $lang} ?? ''),
                180
            );

        /*
        |--------------------------------------------------------------------------
        | Filament uploaded images
        |--------------------------------------------------------------------------
        */

        $imageUrl = function (?string $path): string {

            if (blank($path)) {
                return asset('frontend/img/logo.png');
            }

            if (Str::startsWith(
                $path,
                ['https://', 'http://']
            )) {
                return $path;
            }

            $url = Storage::disk('public')->url($path);

            return Str::startsWith(
                $url,
                ['https://', 'http://']
            )
                ? $url
                : url($url);
        };

        /*
        |--------------------------------------------------------------------------
        | Dynamic category SEO
        |--------------------------------------------------------------------------
        */

        $categoryName =
            $category->{'name_' . $lang}
            ?: $category->name_bn;

        $categoryDescription =
            $category->{'description_' . $lang}
            ?: '';

        $seoTitle =
            $category->{'seo_title_' . $lang}
            ?: $categoryName . ' | Daily Dariyanagar';

        $seoDescription =
            $category->{'seo_description_' . $lang}
            ?: $categoryDescription
            ?: (
                $lang === 'bn'
                    ? $categoryName . ' বিভাগের সর্বশেষ সংবাদ ও প্রতিবেদন।'
                    : 'Latest news and reports in ' . $categoryName . '.'
            );

        $ogTitle =
            $category->{'og_title_' . $lang}
            ?: $seoTitle;

        $ogDescription =
            $category->{'og_description_' . $lang}
            ?: $seoDescription;

        $canonical = $category->{'canonical_url_' . $lang};

        if (
            ! filter_var($canonical, FILTER_VALIDATE_URL)
            || $posts->currentPage() > 1
        ) {
            $canonical = $categoryUrl($category);

            if ($posts->currentPage() > 1) {
                $canonical .= (
                    str_contains($canonical, '?') ? '&' : '?'
                ) . 'page=' . $posts->currentPage();
            }
        }

        $robots = $preview || $category->noindex
            ? 'noindex'
            : 'index';

        $robots .= $category->nofollow
            ? ', nofollow'
            : ', follow';

        /*
        |--------------------------------------------------------------------------
        | Dynamic navigation from NewsCategory
        |--------------------------------------------------------------------------
        */

        $navigation = $categories
            ->map(fn ($item) => [
                'bn' => $item->name_bn,
                'en' => $item->name_en,
                'url' => $categoryUrl($item),
                'active' => $item->id === $category->id,
            ])
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Breaking news data for ticker
        |--------------------------------------------------------------------------
        */

        $ticker = $breakingPosts
            ->map(fn ($item) => [
                'bn' => $item->title_bn,
                'en' => $item->title_en,
                'url' => $postUrl($item),
            ])
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Render Category Blade
        |--------------------------------------------------------------------------
        */

        return view('home.category', compact(
            'category',
            'categories',
            'lang',
            'preview',
            'posts',
            'lead',
            'listPosts',
            'sort',
            'latestPosts',
            'mostReadPosts',
            'breakingPosts',
            'categoryName',
            'categoryDescription',
            'categoryUrl',
            'postUrl',
            'title',
            'summary',
            'imageUrl',
            'seoTitle',
            'seoDescription',
            'ogTitle',
            'ogDescription',
            'canonical',
            'robots',
            'navigation',
            'ticker'
        ));
    }
}