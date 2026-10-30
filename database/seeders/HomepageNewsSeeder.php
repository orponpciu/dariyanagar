<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class HomepageNewsSeeder extends Seeder
{
    private array $items = [];

    private const CATEGORIES = [
        'national' => ['জাতীয়', 'National'],
        'chattogram' => ['চট্টগ্রাম', 'Chattogram'],
        'international' => ['আন্তর্জাতিক', 'International'],
        'politics' => ['রাজনীতি', 'Politics'],
        'economy' => ['অর্থ-বাণিজ্য', 'Economy & Trade'],
        'sports' => ['খেলাধুলা', 'Sports'],
        'entertainment' => ['বিনোদন', 'Entertainment'],
        'technology' => ['তথ্যপ্রযুক্তি', 'Tech & AI'],
        'video' => ['ভিডিও', 'Video'],
        'investigation' => ['অনুসন্ধান', 'Investigation'],
        'health' => ['স্বাস্থ্য ও চিকিৎসা', 'Health & Wellness'],
        'education' => ['শিক্ষা', 'Education'],
        'law-courts' => ['আইন ও আদালত', 'Law & Courts'],
        'opinion' => ['মতামত', 'Opinion & Op-Ed'],
        'interviews' => ['সাক্ষাৎকার', 'Exclusive Interview'],
        'photo-gallery' => ['ছবিঘর', 'Photo Gallery'],
        'lifestyle' => ['লাইফস্টাইল', 'Lifestyle'],
        'journal' => [
            'ডেইলি দরিয়ানগর জার্নাল',
            'Daily Dariyanagar Journal'
        ],
    ];

    public function run(): void
    {
        /*
        |--------------------------------------------
        | 1. READ EXISTING HOMEPAGE
        |--------------------------------------------
        */

        $bladeFile = resource_path(
            'views/home/index.blade.php'
        );

        $jsFile = public_path(
            'frontend/js/script.js'
        );

        if (!is_file($bladeFile) || !is_file($jsFile)) {
            throw new RuntimeException(
                "Homepage files missing:\n"
                . $bladeFile . "\n"
                . $jsFile
            );
        }

        $html = file_get_contents($bladeFile);
        $javascript = file_get_contents($jsFile);

        /*
        |--------------------------------------------
        | 2. EXTRACT ALL STORIES
        |--------------------------------------------
        */

        $this->readJavascript($javascript);
        $this->readHomepageHtml($html);
        $this->readTicker($javascript);

        $total = count($this->items);

        if ($total < 100) {
            throw new RuntimeException(
                "Only {$total} stories found. "
                . "Check the original homepage HTML and JS. "
                . "No database records were inserted."
            );
        }

        /*
        |--------------------------------------------
        | 3. FIND AN EXISTING AUTHOR
        |--------------------------------------------
        */

        $authorId = DB::table('users')
            ->orderBy('id')
            ->value('id');

        if (!$authorId) {
            throw new RuntimeException(
                'Create a Filament admin user before seeding.'
            );
        }

        /*
        |--------------------------------------------
        | 4. DETECT EXISTING DATABASE COLUMNS
        |--------------------------------------------
        */

        $categoryColumns = array_flip(
            Schema::getColumnListing('news_categories')
        );

        $postColumns = array_flip(
            Schema::getColumnListing('news_posts')
        );

        foreach ([
            'news_category_id',
            'title_bn',
            'title_en',
            'slug',
            'body_bn',
            'body_en',
            'status'
        ] as $field) {
            if (!isset($postColumns[$field])) {
                throw new RuntimeException(
                    "Missing news_posts column: {$field}"
                );
            }
        }

        /*
        |--------------------------------------------
        | 5. SEED ALL CATEGORIES
        |--------------------------------------------
        */

        $categoryIds = [];

        foreach (
            self::CATEGORIES as $slug => [$bn, $en]
        ) {
            // Recognize an earlier tech-ai category.
            $existingSlug = $slug === 'technology'
                ? DB::table('news_categories')
                    ->whereIn(
                        'slug',
                        ['technology', 'tech-ai']
                    )
                    ->value('slug')
                : null;

            $categorySlug = $existingSlug ?: $slug;

            $record = [
                'name_bn' => $bn,
                'name_en' => $en,
                'slug' => $categorySlug,

                'description_bn' =>
                    "{$bn} বিভাগের সংবাদ, প্রতিবেদন ও বিশ্লেষণ।",

                'description_en' =>
                    "Reports and updates in {$en}.",

                'seo_title_bn' =>
                    "{$bn} সংবাদ | ডেইলি দরিয়ানগর",

                'seo_title_en' =>
                    "{$en} News | Daily Dariyanagar",

                'seo_description_bn' =>
                    "ডেইলি দরিয়ানগরের {$bn} বিভাগের খবর ও বিশ্লেষণ।",

                'seo_description_en' =>
                    "{$en} news and analysis from Daily Dariyanagar.",

                'focus_keyword_bn' => "{$bn} সংবাদ",
                'focus_keyword_en' => "{$en} News",

                'robots_index' => false,
                'robots_follow' => true,

                'is_active' => true,

                'sort_order' => count($categoryIds) + 1,

                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Existing categories are not overwritten.
            DB::table('news_categories')
                ->insertOrIgnore([
                    $this->onlyColumns(
                        $record,
                        $categoryColumns
                    )
                ]);

            $categoryIds[$slug] = DB::table(
                'news_categories'
            )
                ->where('slug', $categorySlug)
                ->value('id');
        }

        /*
        |--------------------------------------------
        | 6. OPTIONAL IMAGE DOWNLOADING
        |--------------------------------------------
        */

        $downloadImages = filter_var(
            env('SEED_HOME_IMAGES', false),
            FILTER_VALIDATE_BOOLEAN
        );

        $inserted = 0;
        $skipped = 0;
        $images = 0;

        /*
        |--------------------------------------------
        | 7. SEED ALL NEWS POSTS
        |--------------------------------------------
        */

        foreach (
            array_values($this->items) as $index => $item
        ) {
            $slug = Str::slug($item['title_en']);

            if ($slug === '') {
                $slug = 'homepage-story-' . ($index + 1);
            }

            $existing = DB::table('news_posts')
                ->where('slug', $slug)
                ->first();

            $imagePath = null;

            if (
                $downloadImages
                && !empty($item['image'])
            ) {
                $imagePath = $this->downloadImage(
                    $item['image'],
                    $slug
                );

                if ($imagePath) {
                    $images++;
                }
            }

            /*
            |----------------------------------------
            | Do not overwrite previously edited news
            |----------------------------------------
            */

            if ($existing) {
                if (
                    $imagePath
                    && empty($existing->featured_image)
                    && isset($postColumns['featured_image'])
                ) {
                    DB::table('news_posts')
                        ->where('id', $existing->id)
                        ->update([
                            'featured_image' => $imagePath,
                            'updated_at' => now(),
                        ]);
                }

                $skipped++;
                continue;
            }

            $category = $item['category'];

            /*
            |----------------------------------------
            | Preserve existing homepage summaries
            | or generate missing summaries.
            |----------------------------------------
            */

            $bnSummary = $item['excerpt_bn']
                ?: $this->generatedSummary(
                    $item,
                    'bn'
                );

            $enSummary = $item['excerpt_en']
                ?: $this->generatedSummary(
                    $item,
                    'en'
                );

            /*
            |----------------------------------------
            | Generate complete fictional reports
            | in Bengali and English.
            |----------------------------------------
            */

            $bodyBn = $this->report(
                $item,
                $bnSummary,
                'bn'
            );

            $bodyEn = $this->report(
                $item,
                $enSummary,
                'en'
            );

            $record = [

                // Category and author
                'news_category_id' =>
                    $categoryIds[$category],

                'author_id' => $authorId,

                // News headlines
                'title_bn' => $item['title_bn'],
                'title_en' => $item['title_en'],

                'slug' => $slug,

                // Homepage summaries
                'excerpt_bn' => $bnSummary,
                'excerpt_en' => $enSummary,

                // Full article details
                'body_bn' => $bodyBn,
                'body_en' => $bodyEn,

                // Reporter
                'reporter_bn' => 'ডেমো নিউজ ডেস্ক',
                'reporter_en' => 'Demo News Desk',

                // Compatibility with older byline fields
                'byline_bn' => 'ডেমো নিউজ ডেস্ক',
                'byline_en' => 'Demo News Desk',

                // Image information
                'featured_image' => $imagePath,

                'image_alt_bn' => $item['title_bn'],
                'image_alt_en' => $item['title_en'],

                'image_caption_bn' =>
                    'প্রতীকী ছবি; ঘটনার প্রমাণ নয়।',

                'image_caption_en' =>
                    'Illustrative image; not evidence of the event.',

                'image_credit' => $imagePath
                    ? 'Unsplash (illustrative)'
                    : null,

                // Source
                'source_name' =>
                    'Homepage demo source — not verified',

                // Publishing
                'status' => 'draft',
                'published_at' => null,

                'is_breaking' =>
                    $item['ticker'] ?? false,

                'is_featured' => $index === 0,

                'is_trending' =>
                    $item['trending'] ?? false,

                'views' => 0,

                // Technical SEO
                'robots_index' => false,
                'robots_follow' => true,

                // Compatibility with older schema
                'noindex' => true,
                'nofollow' => false,

                // Bengali SEO
                'seo_title_bn' => $item['title_bn'],

                'seo_description_bn' => Str::limit(
                    'কাল্পনিক ডেমো: ' . $bnSummary,
                    200
                ),

                'focus_keyword_bn' =>
                    self::CATEGORIES[$category][0],

                // English SEO
                'seo_title_en' => $item['title_en'],

                'seo_description_en' => Str::limit(
                    'Fictional demo: ' . $enSummary,
                    200
                ),

                'focus_keyword_en' =>
                    self::CATEGORIES[$category][1],

                // Open Graph
                'og_title_bn' => $item['title_bn'],
                'og_title_en' => $item['title_en'],

                'og_description_bn' => Str::limit(
                    $bnSummary,
                    300
                ),

                'og_description_en' => Str::limit(
                    $enSummary,
                    300
                ),

                // Twitter / X
                'twitter_title_bn' =>
                    $item['title_bn'],

                'twitter_title_en' =>
                    $item['title_en'],

                'twitter_description_bn' => Str::limit(
                    $bnSummary,
                    300
                ),

                'twitter_description_en' => Str::limit(
                    $enSummary,
                    300
                ),

                // Schema
                'schema_type' => 'NewsArticle',

                'created_at' => now(),
                'updated_at' => now(),
            ];

            /*
            |----------------------------------------
            | Insert only columns that exist.
            |----------------------------------------
            */

            DB::table('news_posts')->insert(
                $this->onlyColumns(
                    $record,
                    $postColumns
                )
            );

            $inserted++;
        }

        $this->command?->info(
            "Homepage stories: {$total}; "
            . "Inserted: {$inserted}; "
            . "Existing: {$skipped}; "
            . "Images: {$images}; "
            . "Categories: " . count($categoryIds)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: FILTER DATABASE COLUMNS
    |--------------------------------------------------------------------------
    */

    private function onlyColumns(
        array $record,
        array $available
    ): array {
        return array_intersect_key(
            $record,
            $available
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER: ADD A UNIQUE STORY
    |--------------------------------------------------------------------------
    */

    private function addStory(
        string $bn,
        string $en,
        string $category,
        ?string $image = null,
        ?string $bnExcerpt = null,
        ?string $enExcerpt = null,
        bool $trending = false,
        bool $ticker = false
    ): ?string {

        $bn = trim(
            html_entity_decode(
                $bn,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            )
        );

        $en = trim(
            html_entity_decode(
                $en,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            )
        );

        if ($bn === '' || $en === '') {
            return null;
        }

        $key = strtolower($en);

        if (!isset($this->items[$key])) {

            $this->items[$key] = [
                'title_bn' => $bn,
                'title_en' => $en,
                'category' => $category,
                'image' => $image,
                'excerpt_bn' => $bnExcerpt,
                'excerpt_en' => $enExcerpt,
                'trending' => $trending,
                'ticker' => $ticker,
            ];

        } else {

            $story = &$this->items[$key];

            $story['image'] ??= $image;
            $story['excerpt_bn'] ??= $bnExcerpt;
            $story['excerpt_en'] ??= $enExcerpt;

            $story['trending'] =
                $story['trending'] || $trending;

            $story['ticker'] =
                $story['ticker'] || $ticker;
        }

        return $key;
    }

    /*
    |--------------------------------------------------------------------------
    | READ JAVASCRIPT NEWS DATABASES
    |--------------------------------------------------------------------------
    |
    | Extracts all content from:
    |
    | newsDb
    | mostReadDb
    | discussedDb
    |--------------------------------------------------------------------------
    */

    private function readJavascript(string $js): void
    {
        foreach (
            ['newsDb', 'mostReadDb', 'discussedDb']
            as $list
        ) {

            $pattern = '/const\s+'
                . preg_quote($list, '/')
                . '\s*=\s*\[(.*?)\];/su';

            if (!preg_match($pattern, $js, $match)) {
                throw new RuntimeException(
                    "Missing JavaScript array: {$list}"
                );
            }

            preg_match_all(
                '/\{([^{}]*)\}/su',
                $match[1],
                $objects
            );

            foreach ($objects[1] as $i => $raw) {

                preg_match_all(
                    '/([a-zA-Z_][\w]*)\s*:\s*"((?:\\\\.|[^"\\\\])*)"/u',
                    $raw,
                    $fields,
                    PREG_SET_ORDER
                );

                $row = [];

                foreach ($fields as $field) {
                    $row[$field[1]] = json_decode(
                        '"' . $field[2] . '"',
                        true
                    );
                }

                if (
                    empty($row['title_bn'])
                    || empty($row['title_en'])
                ) {
                    continue;
                }

                /*
                |------------------------------------
                | Assign categories by original
                | homepage section and story order.
                |------------------------------------
                */

                $category = match ($list) {

                    'newsDb' => [
                        'investigation',
                        'chattogram',
                        'chattogram',
                        'chattogram',
                        'chattogram',
                        'economy',
                        'chattogram',
                        'health',
                        'chattogram',
                    ][$i] ?? 'national',

                    'mostReadDb' => [
                        'chattogram',
                        'economy',
                        'economy',
                        'education',
                        'economy',
                        'chattogram',
                    ][$i] ?? 'national',

                    default => [
                        'economy',
                        'politics',
                        'economy',
                        'chattogram',
                    ][$i] ?? 'national',
                };

                $this->addStory(
                    $row['title_bn'],
                    $row['title_en'],
                    $category,
                    $row['img'] ?? null,
                    $row['desc_bn'] ?? null,
                    $row['desc_en'] ?? null,
                    $list === 'discussedDb'
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | READ ALL STATIC HOMEPAGE ARTICLE SECTIONS
    |--------------------------------------------------------------------------
    */

    private function readHomepageHtml(
        string $html
    ): void {

        $headerCategories = [
            'National' => 'national',
            'Politics' => 'politics',
            'Chattogram' => 'chattogram',
            'International' => 'international',
            'Sports' => 'sports',
            'Economy & Trade' => 'economy',
            'Law & Courts' => 'law-courts',
            'Education' => 'education',
            'Opinion & Op-Ed' => 'opinion',
            'Exclusive Interview' => 'interviews',
            'Lifestyle' => 'lifestyle',
            'Entertainment' => 'entertainment',
            'Information Technology' => 'technology',
            'Daily Dariyanagar Journal' => 'journal',
            'Photo Gallery' => 'photo-gallery',
            'Health & Wellness' => 'health',
            'Investigative Desk' => 'investigation',
        ];

        $section = 'chattogram';
        $type = '';
        $image = null;
        $lastKey = null;
        $lastLine = -100;
        $listActive = false;
        $imageCardIndex = 0;

        $lines = preg_split('/\R/u', $html);

        foreach ($lines as $lineNo => $line) {

            /*
            |------------------------------------
            | Detect a section heading.
            |------------------------------------
            */

            if (preg_match(
                '/class="[^"]*\b(news-3col-header|category-header|red-header|gallery-header|health-header|anusondhan-header)\b[^"]*"[^>]*>/u',
                $line,
                $m
            )) {

                $attrs = $this->attributes($m[0]);

                $section = $headerCategories[
                    $attrs['data-en'] ?? ''
                ] ?? $section;

                if (!in_array(
                    $m[1],
                    ['category-header', 'red-header'],
                    true
                )) {
                    $type = '';
                }

                $lastKey = null;
                $image = null;
            }

            /*
            |------------------------------------
            | Detect news item container.
            |------------------------------------
            */

            if (
                preg_match(
                    '/\bclass="([^"]+)"/u',
                    $line,
                    $m
                )
                &&
                preg_match(
                    '/(?:^|\s)(img-card|discussed-article|news-3col-item|news-3col-block-item|category-block|red-block|gallery-item|health-item|anusondhan-featured)(?:\s|$)/u',
                    $m[1],
                    $class
                )
            ) {

                $type = $class[1];

                $image = null;
                $lastKey = null;

                /*
                |--------------------------------
                | Six featured image cards.
                |--------------------------------
                */

                if ($type === 'img-card') {

                    $section = [
                        'chattogram',
                        'chattogram',
                        'politics',
                        'politics',
                        'chattogram',
                        'technology',
                    ][$imageCardIndex++] ?? 'chattogram';
                }

                if (in_array(
                    $type,
                    [
                        'discussed-article',
                        'anusondhan-featured'
                    ],
                    true
                )) {
                    $section = 'investigation';
                }

                if ($type === 'gallery-item') {
                    $section = 'photo-gallery';
                }

                if ($type === 'health-item') {
                    $section = 'health';
                }
            }

            /*
            |------------------------------------
            | Detect headline bullet lists.
            |------------------------------------
            */

            if (preg_match(
                '/<ul\b[^>]*class="[^"]*\b(anusondhan-list|news-3col-bullet-list|cat-list)\b[^"]*"/u',
                $line
            )) {
                $listActive = true;
            }

            if (str_contains($line, '</ul>')) {
                $listActive = false;
            }

            /*
            |------------------------------------
            | Read existing illustrative image.
            |------------------------------------
            */

            if (
                $type
                && preg_match(
                    '/<img\b[^>]*>/u',
                    $line,
                    $m
                )
            ) {

                $attrs = $this->attributes($m[0]);

                if (str_starts_with(
                    $attrs['src'] ?? '',
                    'https://images.unsplash.com/'
                )) {
                    $image = $attrs['src'];
                }
            }

            /*
            |------------------------------------
            | Read full section article titles.
            |------------------------------------
            */

            if (
                $type
                && preg_match(
                    '/<h4\b[^>]*data-bn="[^"]*"[^>]*>/u',
                    $line,
                    $m
                )
            ) {

                $attrs = $this->attributes($m[0]);

                if (isset(
                    $attrs['data-bn'],
                    $attrs['data-en']
                )) {

                    $lastKey = $this->addStory(
                        $attrs['data-bn'],
                        $attrs['data-en'],
                        $section,
                        $image
                    );

                    $lastLine = $lineNo;
                }
            }

            /*
            |------------------------------------
            | Read short article bullet titles.
            |------------------------------------
            */

            if (
                $listActive
                && preg_match(
                    '/<li\b[^>]*data-bn="[^"]*"[^>]*>/u',
                    $line,
                    $m
                )
            ) {

                $attrs = $this->attributes($m[0]);

                if (isset(
                    $attrs['data-bn'],
                    $attrs['data-en']
                )) {

                    $this->addStory(
                        $attrs['data-bn'],
                        $attrs['data-en'],
                        $section
                    );
                }

                $lastKey = null;
            }

            /*
            |------------------------------------
            | Health and photo-gallery items
            | use paragraphs as their titles.
            |------------------------------------
            */

            if (
                in_array(
                    $type,
                    ['gallery-item', 'health-item'],
                    true
                )
                && preg_match(
                    '/<p\b[^>]*data-bn="[^"]*"[^>]*>/u',
                    $line,
                    $m
                )
            ) {

                $attrs = $this->attributes($m[0]);

                if (isset(
                    $attrs['data-bn'],
                    $attrs['data-en']
                )) {

                    $this->addStory(
                        $attrs['data-bn'],
                        $attrs['data-en'],
                        $section,
                        $image
                    );
                }

                $lastKey = null;

            } elseif (
                $lastKey
                && $lineNo > $lastLine
                && $lineNo <= $lastLine + 8
            ) {

                /*
                |--------------------------------
                | Capture Bengali/English
                | paragraph summaries.
                |--------------------------------
                */

                if (preg_match(
                    '/<(?:p|span)\b[^>]*data-bn="[^"]*"[^>]*>/u',
                    $line,
                    $m
                )) {

                    $attrs = $this->attributes(
                        $m[0]
                    );

                    if (isset(
                        $attrs['data-bn'],
                        $attrs['data-en']
                    )) {

                        $this->items[$lastKey][
                            'excerpt_bn'
                        ] ??= $attrs['data-bn'];

                        $this->items[$lastKey][
                            'excerpt_en'
                        ] ??= $attrs['data-en'];

                        $lastKey = null;
                    }
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | READ BREAKING NEWS TICKER STORIES
    |--------------------------------------------------------------------------
    */

    private function readTicker(
        string $js
    ): void {

        if (!preg_match(
            '/const\s+tickerTextData\s*=\s*\{(.*?)\};/su',
            $js,
            $m
        )) {
            return;
        }

        preg_match(
            '/\bbn\s*:\s*"((?:\\\\.|[^"\\\\])*)"/u',
            $m[1],
            $bn
        );

        preg_match(
            '/\ben\s*:\s*"((?:\\\\.|[^"\\\\])*)"/u',
            $m[1],
            $en
        );

        if (!isset($bn[1], $en[1])) {
            return;
        }

        $bnText = json_decode(
            '"' . $bn[1] . '"',
            true
        );

        $enText = json_decode(
            '"' . $en[1] . '"',
            true
        );

        $bnLines = explode(' • ', $bnText);
        $enLines = explode(' • ', $enText);

        foreach ($bnLines as $i => $bnLine) {

            if (!isset($enLines[$i])) {
                continue;
            }

            $this->addStory(
                trim($bnLine, " .\t\n\r\0\x0B"),
                trim($enLines[$i], " .\t\n\r\0\x0B"),
                $i === 0 ? 'national' : 'chattogram',
                null,
                null,
                null,
                false,
                true
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | READ ORIGINAL HTML ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    private function attributes(
        string $tag
    ): array {

        preg_match_all(
            '/\b(data-bn|data-en|src)="([^"]*)"/u',
            $tag,
            $matches,
            PREG_SET_ORDER
        );

        $out = [];

        foreach ($matches as $m) {
            $out[$m[1]] = html_entity_decode(
                $m[2],
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE MISSING SUMMARIES
    |--------------------------------------------------------------------------
    */

    private function generatedSummary(
        array $item,
        string $lang
    ): string {

        if ($lang === 'bn') {
            return
                'এই শিরোনামকে কেন্দ্র করে তৈরি কাল্পনিক '
                . 'নমুনা প্রতিবেদন। বিষয়টির পটভূমি, '
                . 'সম্ভাব্য প্রভাব এবং প্রয়োজনীয় যাচাইয়ের '
                . 'দিকগুলো এখানে আলোচিত হয়েছে: '
                . $item['title_bn'] . '।';
        }

        return
            'A fictional demonstration report exploring '
            . 'the background, possible consequences and '
            . 'verification questions surrounding this headline: '
            . $item['title_en'] . '.';
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE FULL BILINGUAL DEMO ARTICLE
    |--------------------------------------------------------------------------
    |
    | Every article has its own headline and source summary.
    | Articles also get subject-specific and category-specific
    | reporting sections.
    |
    | All articles are explicitly fictional drafts.
    |--------------------------------------------------------------------------
    */

    private function report(
        array $item,
        string $summary,
        string $lang
    ): string {

        $title = htmlspecialchars(
            $item['title_' . $lang],
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $intro = htmlspecialchars(
            $summary,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        [
            $categoryBn,
            $categoryEn
        ] = self::CATEGORIES[$item['category']];

        /*
        |--------------------------------------------
        | Category-specific editorial context
        |--------------------------------------------
        */

        $angles = [

            'national' => [
                'জনসেবা, প্রশাসনিক দায়িত্ব, নাগরিক অধিকার ও নীতিগত সিদ্ধান্তের সম্ভাব্য প্রভাব এখানে গুরুত্বপূর্ণ।',
                'Key issues include public services, government accountability, citizen rights and potential policy consequences.'
            ],

            'chattogram' => [
                'চট্টগ্রামের জনজীবন, যাতায়াত, স্থানীয় অর্থনীতি ও এলাকার মানুষের নিরাপত্তাকে ঘিরে সম্ভাব্য পরিবর্তন এই কাহিনির মূল পটভূমি।',
                'The story is situated in Chattogram, with attention to daily life, transport, the local economy and public safety.'
            ],

            'international' => [
                'আন্তর্জাতিক কূটনীতি, উদ্ধার সহায়তা, বাণিজ্য ও আঞ্চলিক নিরাপত্তার পারস্পরিক সম্পর্ক এই কাল্পনিক প্রতিবেদনের প্রেক্ষাপট।',
                'The fictional scenario considers diplomacy, rescue coordination, trade and regional security.'
            ],

            'politics' => [
                'রাজনৈতিক বক্তব্যের বিশ্বাসযোগ্যতা, ভিন্নমত, নির্বাচনী পরিবেশ ও জনমতের ওপর সম্ভাব্য প্রভাব আলাদাভাবে বিবেচনা করা হয়েছে।',
                'The sample considers the reliability of political claims, competing perspectives, election conditions and public opinion.'
            ],

            'economy' => [
                'বিনিয়োগ, কর্মসংস্থান, মূল্যস্ফীতি, সরবরাহব্যবস্থা এবং সাধারণ মানুষের ব্যয়ের ওপর সম্ভাব্য প্রভাব বিশ্লেষণের বিষয়।',
                'Relevant considerations include investment, employment, prices, supply chains and household costs.'
            ],

            'sports' => [
                'খেলার ঘটনাপ্রবাহ, কৌশল, খেলোয়াড়দের পারফরম্যান্স এবং সমর্থকদের প্রতিক্রিয়া এই ক্রীড়া কাহিনির গুরুত্বপূর্ণ উপাদান।',
                'The sporting scenario focuses on match events, tactics, player performances and supporters.'
            ],

            'entertainment' => [
                'চলচ্চিত্র, নাটক, গান ও দর্শকের পছন্দের পরিবর্তনের সঙ্গে সৃজনশীল কাজের সম্পর্ক এখানে আলোচনায় এসেছে।',
                'The sample looks at creative work, screen and music culture, and changing audience tastes.'
            ],

            'technology' => [
                'প্রযুক্তির ব্যবহারিক সুবিধা, তথ্যের নিরাপত্তা, গ্রহণযোগ্যতা এবং সাধারণ ব্যবহারকারীর অভিজ্ঞতা বিবেচনা করা হয়েছে।',
                'The story considers practical benefits, privacy, adoption and everyday technology use.'
            ],

            'video' => [
                'ভিডিওর উৎস, ধারণের সময়, সম্পাদনা ও সত্যতা যাচাইয়ের বিষয়গুলো গুরুত্বপূর্ণ।',
                'The origin, recording time, editing and authenticity of video material require scrutiny.'
            ],

            'investigation' => [
                'অভিযোগ ও প্রমাণকে আলাদা রাখা, সংশ্লিষ্ট সবার বক্তব্য নেওয়া এবং স্বাধীন নথি যাচাই করা অনুসন্ধানের আবশ্যিক ধাপ।',
                'Investigative work must separate allegations from evidence, seek responses from all sides and independently verify records.'
            ],

            'health' => [
                'রোগীর নিরাপত্তা, চিকিৎসা-সংক্রান্ত প্রমাণ, বিশেষজ্ঞ মতামত এবং ব্যক্তি ভেদে ঝুঁকির পার্থক্য বিবেচনা জরুরি।',
                'Patient safety, clinical evidence, expert review and individual differences in health risks are essential.'
            ],

            'education' => [
                'শিক্ষার্থী, শিক্ষক, অভিভাবক ও শিক্ষাপ্রতিষ্ঠানের ওপর সম্ভাব্য প্রভাব এবং বাস্তবায়নের প্রস্তুতি প্রধান বিবেচনা।',
                'The sample examines potential effects on students, teachers, families and institutions.'
            ],

            'law-courts' => [
                'আদালতের আদেশ, মামলার নথি এবং অভিযোগ ও রায়ের পার্থক্য নিশ্চিত না করে কোনো সিদ্ধান্তে পৌঁছানো যায় না।',
                'Orders and case records must be examined carefully, distinguishing allegations, proceedings and judgments.'
            ],

            'opinion' => [
                'এই অংশটি মতামতমূলক; এখানে ভিন্ন ব্যাখ্যা এবং যুক্তির সীমাবদ্ধতাও স্বীকার করা হয়েছে।',
                'This is opinion-style sample writing, acknowledging alternative interpretations and limits to the argument.'
            ],

            'interviews' => [
                'যে বক্তব্যের কথা শিরোনামে এসেছে, তা কেবল মূল সাক্ষাৎকারের রেকর্ড ও সঠিক উদ্ধৃতির সঙ্গে মিলিয়েই প্রকাশযোগ্য।',
                'Any attributed interview statement must be checked against an original recording and accurate quotation.'
            ],

            'photo-gallery' => [
                'ছবির স্থান, সময়, আলোকচিত্রী ও দৃশ্যের সঠিক পরিচয় নিশ্চিত করা একটি দায়িত্বশীল ছবিঘরের অংশ।',
                'Responsible photo coverage requires accurate locations, dates, photographer credits and image descriptions.'
            ],

            'lifestyle' => [
                'জীবনযাত্রার পরামর্শ সবার ক্ষেত্রে সমান কার্যকর হয় না; গবেষণা, বয়স ও ব্যক্তিগত পরিস্থিতি গুরুত্বপূর্ণ।',
                'Lifestyle guidance varies by individual circumstances, and claims should be checked against reliable evidence.'
            ],

            'journal' => [
                'সংবাদের ভাষা, তথ্য যাচাই ও গণমাধ্যমের দায়বদ্ধতা নিয়ে বিশ্লেষণ এই জার্নালের মূল লক্ষ্য।',
                'The journal-style article explores reporting language, verification and media accountability.'
            ],

        ];

        [
            $angleBn,
            $angleEn
        ] = $angles[$item['category']];

        $storyDetail = htmlspecialchars(
            $this->storyDetail($item, $lang),
            ENT_QUOTES,
            'UTF-8'
        );

        /*
        |--------------------------------------------
        | Bengali article
        |--------------------------------------------
        */

        if ($lang === 'bn') {

            $categoryName = htmlspecialchars(
                $categoryBn,
                ENT_QUOTES,
                'UTF-8'
            );

            $paragraphs = [

                '<p><strong>কাল্পনিক ডেমো প্রতিবেদন — বাস্তব বা যাচাইকৃত সংবাদ নয়।</strong> এই লেখা কেবল ডেইলি দরিয়ানগরের সংবাদ ব্যবস্থাপনা সিস্টেম পরীক্ষা ও ডিজাইন প্রদর্শনের উদ্দেশ্যে তৈরি। কোনো ব্যক্তি, প্রতিষ্ঠান, ঘটনা বা উদ্ধৃতি সম্পর্কে এটিকে সত্য তথ্য হিসেবে ব্যবহার করা যাবে না।</p>',

                "<h2>{$title}</h2>",

                "<p>{$intro}</p>",

                "<p>এটি একটি পরিকল্পিত নমুনা দৃশ্যপট। “{$title}”—এই বিষয়কে কেন্দ্র করে ঘটনার সম্ভাব্য ধারাবাহিকতা, সংশ্লিষ্ট পক্ষের উদ্বেগ এবং সাধারণ পাঠকের জিজ্ঞাসা সাজানো হয়েছে। বাস্তব প্রতিবেদনে ঘটনাস্থল, তারিখ, প্রত্যক্ষদর্শী এবং প্রাতিষ্ঠানিক বক্তব্য আলাদাভাবে নিশ্চিত করা প্রয়োজন।</p>",

                '<h2>প্রেক্ষাপট ও সম্ভাব্য প্রভাব</h2>',

                '<p>' .
                    htmlspecialchars(
                        $angleBn,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                    ' এই নমুনায় সেই আলোচনার সঙ্গে মানুষের দৈনন্দিন অভিজ্ঞতা, প্রশাসনিক সক্ষমতা এবং দীর্ঘমেয়াদি পরিণতির সম্পর্ক কল্পনাভিত্তিকভাবে তুলে ধরা হয়েছে।</p>',

                "<p>এ ধরনের পরিস্থিতিতে সবচেয়ে গুরুত্বপূর্ণ প্রশ্ন হলো, কে কী দাবি করছেন, দাবির প্রমাণ কোথায়, ঘটনা ঘটলে কারা সরাসরি প্রভাবিত হতে পারেন এবং সমাধানের দায়িত্ব কোন প্রতিষ্ঠানের। “{$title}” প্রসঙ্গে এই প্রশ্নগুলোর উত্তর যাচাই ছাড়া নিশ্চিত করে লেখা দায়িত্বশীল সাংবাদিকতার সঙ্গে সামঞ্জস্যপূর্ণ নয়।</p>",

                '<h2>ঘটনাটির বিশেষ দিক</h2>',

                "<p>{$storyDetail}</p>",

                '<h2>সংশ্লিষ্ট পক্ষ ও পরবর্তী করণীয়</h2>',

                '<p>একটি পূর্ণাঙ্গ বাস্তব প্রতিবেদনে ভুক্তভোগী বা প্রত্যক্ষ অংশগ্রহণকারীর অভিজ্ঞতা, স্থানীয় প্রশাসন বা সংশ্লিষ্ট প্রতিষ্ঠানের বক্তব্য, স্বাধীন বিশেষজ্ঞের মূল্যায়ন এবং প্রাসঙ্গিক নথি প্রয়োজন। সম্ভাব্য সামাজিক ও অর্থনৈতিক প্রভাবও পৃথকভাবে যাচাই করতে হবে।</p>',

                "<p>এই {$categoryName} বিভাগের কল্পিত প্রতিবেদনটি একটি সম্পাদকীয় খসড়া। উৎস যাচাই, স্বাধীন সাক্ষাৎকার, প্রাসঙ্গিক দলিল এবং প্রয়োজনীয় সংশোধন ছাড়া এটি প্রকাশ করা উচিত নয়।</p>",

            ];

        } else {

            /*
            |----------------------------------------
            | English article
            |----------------------------------------
            */

            $categoryName = htmlspecialchars(
                $categoryEn,
                ENT_QUOTES,
                'UTF-8'
            );

            $paragraphs = [

                '<p><strong>FICTIONAL DEMO ARTICLE — NOT VERIFIED NEWS.</strong> This text was created solely to test and demonstrate the Daily Dariyanagar content-management system. It must not be represented as an actual event, a verified allegation, or a genuine quotation.</p>',

                "<h2>{$title}</h2>",

                "<p>{$intro}</p>",

                "<p>This is a constructed editorial scenario inspired by the headline “{$title}”. It explores a possible sequence of developments, questions that residents or readers might ask, and the responsibilities of organizations that could be involved. A real report would need confirmed dates, locations, firsthand accounts and attributable sources.</p>",

                '<h2>Background and possible implications</h2>',

                '<p>' .
                    htmlspecialchars(
                        $angleEn,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                    ' The hypothetical story also considers how the situation might affect people going about their daily lives and which longer-term consequences would merit further attention.</p>',

                "<p>Before treating the premise of “{$title}” as fact, an editor would need to establish precisely what happened, who is making each claim, what documentary evidence exists and how any potentially affected people respond. Claims, opinions, estimates and established facts must not be confused.</p>",

                '<h2>Specific questions raised by this story</h2>',

                "<p>{$storyDetail}</p>",

                '<h2>Responses and next steps</h2>',

                '<p>A complete, publishable report would include responses from relevant parties, independent expert scrutiny where appropriate, documentary confirmation and useful context for readers. Any statistics, medical statements, attributed quotations or allegations would require particularly careful checking.</p>',

                "<p>This draft in the {$categoryName} section is intentionally fictional. It must be researched, fact-checked and rewritten with verified primary sources before any public publication.</p>",

            ];
        }

        return implode("\n", $paragraphs);
    }

    /*
    |--------------------------------------------------------------------------
    | ADD STORY-SPECIFIC REPORTING CONTEXT
    |--------------------------------------------------------------------------
    */

    private function storyDetail(
        array $item,
        string $lang
    ): string {

        $topics = [

            [
                '/robbery|salt truck/i',
                'কল্পিত পরিস্থিতিতে পণ্যবাহী গাড়ির রুট, সড়কে নজরদারি, চালকের নিরাপত্তা এবং থানায় অভিযোগ গ্রহণের প্রক্রিয়া গুরুত্বপূর্ণ হয়ে ওঠে।',
                'The fictional situation raises questions about freight routes, roadside surveillance, driver safety and the reporting of a suspected robbery.'
            ],

            [
                '/explosion|gas pipeline/i',
                'কল্পিত দুর্ঘটনার ক্ষেত্রে গ্যাস সরবরাহের নিরাপত্তা, অগ্নিনির্বাপণ প্রস্তুতি, জরুরি চিকিৎসা এবং প্রকৃত ক্ষয়ক্ষতির হিসাব গুরুত্বপূর্ণ।',
                'A fictional explosion scenario would require scrutiny of gas-system safety, emergency response, medical care and verified casualty figures.'
            ],

            [
                '/hill cutting|cutting hills/i',
                'ভূমির মালিকানা, পরিবেশগত অনুমতি, পাহাড় ধসের ঝুঁকি এবং অভিযানের আইনগত ভিত্তি যাচাই করা জরুরি।',
                'Land ownership, environmental permits, landslide risks and legal grounds for enforcement would need examination.'
            ],

            [
                '/pollution|industrial waste|river/i',
                'নমুনা প্রতিবেদনে জল ও মাটির নমুনা পরীক্ষা, দূষণের উৎস শনাক্ত করা এবং ক্ষতিগ্রস্ত পরিবারের বক্তব্য নেওয়ার গুরুত্ব রয়েছে।',
                'The scenario calls for water and soil testing, source tracing and the perspectives of families potentially affected by pollution.'
            ],

            [
                '/employment|jobs|recruited|recruitment/i',
                'নতুন চাকরির দাবির সঙ্গে বাস্তব নিয়োগ বিজ্ঞপ্তি, পদসংখ্যা, যোগ্যতা ও শ্রমিকের অধিকার যাচাই করা প্রয়োজন।',
                'Job claims should be matched to official recruitment notices, position counts, eligibility rules and worker protections.'
            ],

            [
                '/train|railway|shuttle/i',
                'যাত্রীর চাপ, নির্ধারিত সময়সূচি, নিরাপত্তা পরিদর্শন এবং নতুন সেবার অর্থায়ন এই কল্পিত রেলপথ কাহিনির প্রশ্ন।',
                'Passenger demand, published timetables, safety inspections and service funding form the central railway questions.'
            ],

            [
                '/hospital|surgery|healthcare|medicine|drug/i',
                'চিকিৎসার ক্ষেত্রে হাসপাতালের সক্ষমতা, রোগীর সম্মতি, অনুমোদন, ওষুধের প্রাপ্যতা ও পরীক্ষিত চিকিৎসা-ফলাফল গুরুত্বপূর্ণ।',
                'Hospital capacity, informed consent, approvals, medicine availability and validated clinical outcomes require careful review.'
            ],

            [
                '/airport|cargo|port|vessel|terminal|shipping/i',
                'বন্দর ও পরিবহনে সক্ষমতা, শুল্ক প্রক্রিয়া, আন্তর্জাতিক বাণিজ্য এবং স্থানীয় কর্মসংস্থানের সম্ভাব্য প্রভাব আলোচনায় আসে।',
                'Capacity, customs procedures, international logistics and possible employment effects would be examined in a port or airport story.'
            ],

            [
                '/tunnel|highway|roads|traffic|road pipes/i',
                'সড়ক অবকাঠামোর নিরাপত্তা, নির্মাণ অনুমোদন, প্রকল্পের ব্যয় এবং জনদুর্ভোগের বিষয়গুলো পর্যালোচনার দাবি রাখে।',
                'Road safety, construction permits, public expenditure and potential disruption would be important in this transport story.'
            ],

            [
                '/ramadan|essential goods|prices|gold/i',
                'দাম ও সরবরাহের ক্ষেত্রে বাজারের নথি, ক্রেতার অভিজ্ঞতা, ব্যবসায়ীর বক্তব্য ও সরকারি পর্যবেক্ষণ আলাদা করে দেখতে হবে।',
                'Claims about prices and supplies require market records, buyer experiences, trader responses and reliable official monitoring.'
            ],

            [
                '/electricity|power|solar|energy/i',
                'জ্বালানি খাতে প্রকৃত উৎপাদন, ব্যয়ের হিসাব, চুক্তির শর্ত এবং পরিবেশগত প্রভাব বুঝে সিদ্ধান্ত নেওয়া প্রয়োজন।',
                'Energy claims turn on verified generation data, contract conditions, cost accounting and environmental effects.'
            ],

            [
                '/deepfake|bot|ai video|disinformation/i',
                'ভিডিও বা অডিওর মূল উৎস, প্রকাশের সময়, সম্পাদনার চিহ্ন এবং স্বাধীন ডিজিটাল ফরেনসিক পর্যালোচনা ছাড়া সিদ্ধান্ত দেওয়া ঠিক নয়।',
                'Original media files, timestamps, editing traces and independent forensic checks are needed before labeling media as authentic or manipulated.'
            ],

            [
                '/election|politic|minister|mp |bnp|ncp/i',
                'রাজনৈতিক বক্তব্যের প্রেক্ষাপট, রেকর্ডকৃত উদ্ধৃতি এবং সংশ্লিষ্ট পক্ষের প্রতিক্রিয়া আলাদাভাবে যাচাই করা উচিত।',
                'The precise context of political statements, recorded quotations and responses from concerned parties needs verification.'
            ],

            [
                '/flood|rain|sluice|lake|dam/i',
                'পানি উন্নয়ন কর্তৃপক্ষের তথ্য, আবহাওয়ার পূর্বাভাস, মাঠপর্যায়ের ক্ষতি এবং উদ্ধার প্রস্তুতি এই প্রেক্ষাপটে প্রাসঙ্গিক।',
                'Water-level data, forecasts, field assessments of damage and emergency planning are relevant to this scenario.'
            ],

            [
                '/football|messi|brazil|argentina|bangladesh|australia|stadium|sport/i',
                'খেলার বিবরণ তৈরির সময় প্রতিযোগিতার নাম, স্কোরশিট, খেলোয়াড়ের পরিচয়, রেকর্ড ও ম্যাচের তারিখ নিশ্চিত করতে হবে।',
                'A sports report requires the official competition name, score sheet, player identities, records and match dates.'
            ],

            [
                '/court|prosecutor|tribunal|high court|fine|sentence/i',
                'কোন আদেশ অন্তর্বর্তী, কোনটি চূড়ান্ত এবং কোন বক্তব্য কেবল অভিযোগ—আইনগত প্রেক্ষাপটে এই পার্থক্য অত্যন্ত জরুরি।',
                'The distinction among allegations, interim orders and final judgments is essential in legal reporting.'
            ],

            [
                '/school|student|university|cuet|syndicate|education/i',
                'শিক্ষাপ্রতিষ্ঠানের ক্ষেত্রে নীতিমালা, শিক্ষার্থী-শিক্ষকের অংশগ্রহণ এবং কার্যকর করার সম্ভাব্য সময়সূচি যাচাই প্রয়োজন।',
                'Institutional policies, participation by students and teachers, and practical implementation schedules need checking.'
            ],

            [
                '/photo|fishermen|sunset|garden|crafts|festival/i',
                'এ ধরনের আলোকচিত্র প্রতিবেদনে মানুষের কাজ, স্থানের প্রকৃতি ও ছবির প্রেক্ষাপট ফুটিয়ে তোলার পাশাপাশি সঠিক ক্যাপশন প্রয়োজন।',
                'Photo-led storytelling needs accurate captions and context explaining the people, place and activity depicted.'
            ],

            [
                '/cancer|carrot|heart disease|walking|stress/i',
                'নির্দিষ্ট খাদ্য বা ব্যায়ামে শতকরা ঝুঁকি কমার দাবি যাচাই করার জন্য গুণগত গবেষণা ও চিকিৎসকের মতামত প্রয়োজন।',
                'Claims of percentage risk reductions from foods or exercise need high-quality research and appropriate clinical context.'
            ],

            [
                '/smartphone|technology|ai|cyber|payment gateway|app/i',
                'ব্যবহারকারীর গোপনীয়তা, পণ্য বা অ্যাপের প্রকৃত সক্ষমতা এবং নিরাপত্তা পরীক্ষার ফল আলাদা করে দেখানো দরকার।',
                'Privacy, actual product capabilities and independent security testing matter when assessing digital products and services.'
            ],

            [
                '/investment|bank|loan|export|economic/i',
                'বিনিয়োগ বা আর্থিক অগ্রগতির দাবিতে প্রকল্পের দলিল, অডিটকৃত হিসাব এবং অর্থনৈতিক ঝুঁকি পর্যালোচনা প্রয়োজন।',
                'Investment and financial claims call for project records, audited figures and analysis of economic risks.'
            ],

        ];

        foreach ($topics as [$pattern, $bn, $en]) {

            if (preg_match(
                $pattern,
                $item['title_en']
            )) {
                return $lang === 'bn'
                    ? $bn
                    : $en;
            }
        }

        return $lang === 'bn'
            ? 'এই বিশেষ বিষয়টির সঙ্গে সংশ্লিষ্ট স্থানীয় তথ্য, ঘটনার ক্রম, প্রত্যক্ষ অভিজ্ঞতা ও সিদ্ধান্তের সম্ভাব্য প্রভাবের স্বাধীন যাচাই প্রয়োজন।'
            : 'This particular topic calls for independent checks of its local context, timeline, firsthand accounts and potential consequences.';
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL IMAGE DOWNLOADING
    |--------------------------------------------------------------------------
    |
    | Saves existing homepage Unsplash images to
    | public storage so Filament can display them.
    |--------------------------------------------------------------------------
    */

    private function downloadImage(
        string $url,
        string $slug
    ): ?string {

        if (!str_starts_with(
            $url,
            'https://images.unsplash.com/'
        )) {
            return null;
        }

        $path = 'news/homepage-demo/'
            . $slug . '.jpg';

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {

            $response = Http::timeout(15)
                ->get($url);

            $contentType = strtolower(
                $response->header(
                    'Content-Type',
                    ''
                )
            );

            if (
                !$response->successful()
                || !str_contains(
                    $contentType,
                    'image/'
                )
                || strlen($response->body()) >
                    6 * 1024 * 1024
            ) {
                return null;
            }

            Storage::disk('public')->put(
                $path,
                $response->body()
            );

            return $path;

        } catch (\Throwable $e) {

            $this->command?->warn(
                "Image skipped for {$slug}: "
                . $e->getMessage()
            );

            return null;
        }
    }
}
