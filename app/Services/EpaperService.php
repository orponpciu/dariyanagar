<?php

namespace App\Services;

use App\Models\NewsPost;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EpaperService
{
    public function archive(): array
    {
        return NewsPost::query()
            ->published()
            ->selectRaw('DATE(published_at) as edition_date')
            ->distinct()
            ->orderByDesc('edition_date')
            ->limit((int) config('epaper.max_archive_dates', 180))
            ->pluck('edition_date')
            ->all();
    }

    public function build(?string $requestedDate = null): array
    {
        $archive = $this->archive();

        if ($requestedDate !== null) {

            try {
                $parsed = CarbonImmutable::createFromFormat(
                    '!Y-m-d',
                    $requestedDate
                );
            } catch (\Throwable) {
                abort(404, 'Invalid edition date.');
            }

            abort_unless(
                $parsed &&
                $parsed->format('Y-m-d') === $requestedDate,
                404,
                'Invalid edition date.'
            );
        }

        $date = $requestedDate
            ?? ($archive[0] ?? now()->toDateString());

        /*
        |--------------------------------------------------------------------------
        | Existing Filament posts and categories
        |--------------------------------------------------------------------------
        */

        $posts = NewsPost::query()
            ->published()
            ->with('category')
            ->whereDate('published_at', $date)
            ->orderByDesc('is_featured')
            ->orderByDesc('is_breaking')
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit((int) config('epaper.max_articles', 120))
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Generate physical newspaper-like pages
        |--------------------------------------------------------------------------
        */

        $pages = [];

        if ($posts->isNotEmpty()) {

            $coverCount = (int) config('epaper.cover_count', 5);
            $insideCount = (int) config('epaper.inside_count', 6);

            $pages[] = [
                'number' => 1,
                'kind' => 'cover',
                'stories' => $posts
                    ->take($coverCount)
                    ->values(),
            ];

            foreach (
                $posts->skip($coverCount)->chunk($insideCount)
                as $chunk
            ) {
                $pages[] = [
                    'number' => count($pages) + 1,
                    'kind' => 'inside',
                    'stories' => $chunk->values(),
                ];
            }
        }

        return compact('date', 'archive', 'posts', 'pages');
    }

    public function title(NewsPost $post, string $lang): string
    {
        return (string) (
            $post->{'title_' . $lang}
            ?: $post->title_bn
        );
    }

    public function summary(
        NewsPost $post,
        string $lang,
        int $length = 480
    ): string {

        $text = $post->{'excerpt_' . $lang};

        if (blank($text)) {

            $html = (string) (
                $post->{'body_' . $lang}
                ?: $post->body_bn
            );

            $html = preg_replace(
                '~</(p|div|h[1-6]|li)>~i',
                ' ',
                $html
            ) ?? $html;

            $text = strip_tags($html);
        }

        $text = html_entity_decode(
            (string) $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return Str::limit(trim($text), $length);
    }

    public function category(NewsPost $post, string $lang): string
    {
        return (string) (
            $post->category?->{'name_' . $lang}
            ?: $post->category?->name_bn
            ?: 'সংবাদ'
        );
    }

    public function storyUrl(
        NewsPost $post,
        string $lang
    ): string {

        return route('news.show', [
            'slug' => $post->slug,
            ...(
                $lang === 'en'
                    ? ['lang' => 'en']
                    : []
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Uploaded image URL
    |--------------------------------------------------------------------------
    |
    | Browser: URL for normal image display.
    | PDF: Local filesystem path for mPDF.
    |--------------------------------------------------------------------------
    */

    public function image(
        NewsPost $post,
        bool $forPdf = false
    ): ?string {

        $path = $post->featured_image;

        if (blank($path)) {
            return null;
        }

        if (Str::startsWith(
            $path,
            ['https://', 'http://']
        )) {
            return $forPdf ? null : $path;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return null;
        }

        return $forPdf
            ? $disk->path($path)
            : $disk->url($path);
    }

    /*
    |--------------------------------------------------------------------------
    | Regenerate PDF when article data changes
    |--------------------------------------------------------------------------
    */

    public function cacheKey(array $edition, string $lang): string
    {
        $revisions = $edition['posts']
            ->map(fn (NewsPost $post) => [
                $post->id,
                $post->updated_at?->timestamp,
                $post->category?->updated_at?->timestamp,
                $post->featured_image,
            ])
            ->all();

        return sha1(json_encode([
            'v1',
            $edition['date'],
            $lang,
            $revisions,
            config('epaper'),
        ], JSON_UNESCAPED_UNICODE));
    }
}
