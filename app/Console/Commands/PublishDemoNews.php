<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishDemoNews extends Command
{
    protected $signature = 'news:publish-demo
        {--days=14 : Spread new demo posts over this many days (1 = publish all today)}
        {--dry-run : Show what would be changed without updating the database}';

    protected $description = 'Activate demo news categories and publish only identified demo news posts.';

    public function handle(): int
    {
        $days = filter_var($this->option('days'), FILTER_VALIDATE_INT);

        if ($days === false || $days < 1 || $days > 365) {
            $this->error('--days must be an integer between 1 and 365.');
            return self::FAILURE;
        }

        $demoCategorySlugs = [
            'national', 'chattogram', 'international', 'politics',
            'economy', 'sports', 'entertainment', 'technology', 'tech-ai',
            'video', 'investigation', 'health', 'environment',
            'education', 'law-courts', 'opinion', 'interviews',
            'photo-gallery', 'lifestyle', 'journal',
        ];

        // Both previously supplied seeders use these reliable demo markers.
        $demoPosts = DB::table('news_posts')
            ->where(function ($query) {
                $query->where('source_name', 'Homepage demo source — not verified')
                    ->orWhere('slug', 'like', 'demo-%');
            })
            ->orderBy('id')
            ->get([
                'id', 'news_category_id', 'title_bn', 'title_en',
                'slug', 'status', 'published_at',
            ]);

        $categoryIds = $demoPosts
            ->pluck('news_category_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $categories = DB::table('news_categories')
            ->where(function ($query) use ($demoCategorySlugs, $categoryIds) {
                $query->whereIn('slug', $demoCategorySlugs);
                if ($categoryIds !== []) {
                    $query->orWhereIn('id', $categoryIds);
                }
            })
            ->get(['id', 'slug', 'is_active']);

        $newlyPublished = $demoPosts->filter(
            fn ($post) => $post->status !== 'published'
                || $post->published_at === null
                || \Illuminate\Support\Carbon::parse($post->published_at)->isFuture()
        );

        $this->info('Demo posts found: ' . $demoPosts->count());
        $this->info('Demo posts needing publication: ' . $newlyPublished->count());
        $this->info('Matching categories: ' . $categories->count());
        $this->info('Inactive matching categories: ' . $categories->where('is_active', 0)->count());

        if ($demoPosts->isEmpty()) {
            $this->warn('No matching demo posts are in this database. This command does not create demo posts.');
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run: nothing was changed.');
            return self::SUCCESS;
        }

        $columns = array_flip(Schema::getColumnListing('news_posts'));
        $now = now();
        $publishedCount = 0;

        DB::transaction(function () use (
            $categories, $newlyPublished, $columns, $now, $days, &$publishedCount
        ): void {
            if ($categories->isNotEmpty()) {
                DB::table('news_categories')
                    ->whereIn('id', $categories->pluck('id')->all())
                    ->update(['is_active' => true, 'updated_at' => $now]);
            }

            foreach ($newlyPublished->values() as $index => $post) {
                $changes = [
                    'status' => 'published',
                    'published_at' => $now->copy()->subDays($index % $days),
                    'updated_at' => $now,
                ];

                // Keep fictional demonstration stories excluded from search indexing.
                if (isset($columns['noindex'])) {
                    $changes['noindex'] = true;
                }
                if (isset($columns['robots_index'])) {
                    $changes['robots_index'] = false;
                }

                // Make the demonstration status visible in listing headlines.
                $bnTitle = (string) $post->title_bn;
                $enTitle = (string) $post->title_en;
                if (!str_starts_with($bnTitle, 'ডেমো:')) {
                    $changes['title_bn'] = 'ডেমো: ' . $bnTitle;
                }
                if (!str_starts_with(strtolower($enTitle), 'demo:')) {
                    $changes['title_en'] = 'Demo: ' . $enTitle;
                }

                DB::table('news_posts')->where('id', $post->id)->update($changes);
                $publishedCount++;
            }
        });

        $this->info("Done. Published {$publishedCount} demo posts; activated matching categories.");
        $this->line('Published real editorial drafts were not modified.');
        $this->line('Demo articles remain marked as demonstrations and noindex.');

        return self::SUCCESS;
    }
}
