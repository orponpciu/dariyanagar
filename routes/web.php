
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomepageController;
use App\Http\Controllers\NewsArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EpaperController;

use App\Models\NewsPost;

/*
|--------------------------------------------------------------------------
| DAILY DARIYANAGAR — WEB ROUTES
|--------------------------------------------------------------------------
|
| Laravel 13 + Filament 5
|
| Routes:
| 1. Dynamic Homepage
| 2. News Article Details
| 3. Legacy Post Details Redirect
| 4. Dynamic News Categories
| 5. Dynamic E-Paper Reader
| 6. Full E-Paper PDF Download
| 7. Individual Page PDF Download
| 8. Individual Column PDF Download
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| 1. Dynamic Homepage
|--------------------------------------------------------------------------
|
| Fetches news posts and categories from the Filament backend.
|
| URL: /
|
*/

Route::get('/', [
    HomepageController::class,
    'index',
])->name('home');


/*
|--------------------------------------------------------------------------
| 2. Dynamic Article Details
|--------------------------------------------------------------------------
|
| Displays individual published news articles.
|
| Example:
| /news/example-news-title
|
*/

Route::get('/news/{slug}', [
    NewsArticleController::class,
    'show',
])->name('news.show');


/*
|--------------------------------------------------------------------------
| 3. Legacy Post Details Redirect
|--------------------------------------------------------------------------
|
| Redirects old /post-details links to the latest published article.
|
| URL: /post-details
|
*/

Route::get('/post-details', function () {

    $post = NewsPost::query()
        ->where('status', 'published')
        ->whereNotNull('published_at')
        ->where('published_at', '<=', now())
        ->whereHas('category', function ($query) {

            $query->where('is_active', true);

        })
        ->latest('published_at')
        ->first();

    if (!$post) {

        return redirect()->route('home');

    }

    return redirect()->route('news.show', [
        'slug' => $post->slug,
    ]);

})->name('post.details');


/*
|--------------------------------------------------------------------------
| 4. Dynamic News Category Pages
|--------------------------------------------------------------------------
|
| Categories and their published posts come from Filament.
|
| Examples:
| /category/national
| /category/chattogram
| /category/politics
| /category/sports
|
*/

Route::get('/category/{slug}', [
    CategoryController::class,
    'show',
])->name('category.show');


/*
|--------------------------------------------------------------------------
| 5. Dynamic E-Paper Reader
|--------------------------------------------------------------------------
|
| Vintage E-Paper interface.
|
| Examples:
| /epaper
| /epaper?date=2026-10-29
| /epaper?date=2026-10-29&page=2
| /epaper?lang=en
|
*/

Route::get('/epaper', [
    EpaperController::class,
    'index',
])->name('epaper.index');


/*
|--------------------------------------------------------------------------
| 6. Download Complete E-Paper PDF
|--------------------------------------------------------------------------
|
| Downloads every page of the selected daily edition.
|
| Examples:
| /epaper/download
| /epaper/download?date=2026-10-29
| /epaper/download?date=2026-10-29&lang=en
|
*/

Route::get('/epaper/download', [
    EpaperController::class,
    'download',
])->name('epaper.download');


/*
|--------------------------------------------------------------------------
| 7. Download Individual E-Paper Page PDF
|--------------------------------------------------------------------------
|
| Downloads only the selected newspaper page.
|
| Examples:
| /epaper/download/page/1?date=2026-10-29
| /epaper/download/page/2?date=2026-10-29
| /epaper/download/page/3?date=2026-10-29
|
*/

Route::get('/epaper/download/page/{page}', [
    EpaperController::class,
    'downloadPage',
])
    ->whereNumber('page')
    ->name('epaper.download.page');


/*
|--------------------------------------------------------------------------
| 8. Download Individual E-Paper Column PDF
|--------------------------------------------------------------------------
|
| Downloads a selected column from a selected newspaper page.
|
| Column 1 = Left Column
| Column 2 = Right Column
|
| Examples:
| /epaper/download/column/1/1?date=2026-10-29
| /epaper/download/column/1/2?date=2026-10-29
| /epaper/download/column/2/1?date=2026-10-29
| /epaper/download/column/2/2?date=2026-10-29
|
*/

Route::get('/epaper/download/column/{page}/{column}', [
    EpaperController::class,
    'downloadColumn',
])
    ->whereNumber('page')
    ->where('column', '[12]')
    ->name('epaper.download.column');

