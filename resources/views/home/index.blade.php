
<!DOCTYPE html>
<html lang="{{ $lang }}">

@include('home.css')

<body>

@include('home.header')

@php
    /*
    |--------------------------------------------------------------------------
    | Daily Dariyanagar - Dynamic homepage links
    |--------------------------------------------------------------------------
    | All article links use the NewsPost slug.
    | All category links use the NewsCategory slug.
    | The E-Paper link uses epaper.index.
    */

    $linkParams = [];

    if ($lang === 'en') {
        $linkParams['lang'] = 'en';
    }

    if ($preview) {
        $linkParams['preview'] = 1;
    }

    $categoryLink = function (string $slug) use (
        $categories,
        $linkParams
    ) {
        $category = $categories->firstWhere('slug', $slug);

        if (!$category && $slug === 'technology') {
            $category = $categories->firstWhere('slug', 'tech-ai');
        }

        if (!$category) {
            return route('home', $linkParams);
        }

        return route('category.show', array_merge(
            ['slug' => $category->slug],
            $linkParams
        ));
    };

    $epaperLink = route(
        'epaper.index',
        $lang === 'en' ? ['lang' => 'en'] : []
    );

    $epaperDownloadLink = route(
        'epaper.download',
        $lang === 'en' ? ['lang' => 'en'] : []
    );

    $homeLink = route('home', $linkParams);

    $fallbackImage = asset('frontend/img/logo.png');

    $bnNumber = function ($number) use ($lang) {
        if ($lang === 'en') {
            return (string) $number;
        }

        return strtr((string) $number, [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);
    };

    $readerData = [
        'lang' => $lang,
        'homeUrl' => route('home'),
        'epaperUrl' => $epaperLink,
        'preview' => $preview,
        'search' => $search,
        'navigation' => $categories->map(function ($category) use (
            $categoryLink
        ) {
            return [
                'bn' => $category->name_bn,
                'en' => $category->name_en,
                'url' => $categoryLink($category->slug),
            ];
        })->values()->all(),
        'ticker' => $ticker,
    ];

    /*
    | Social links are only enabled if configured.
    | This avoids sending visitors to placeholder "#" URLs.
    */

    $facebookUrl = config('services.facebook.page_url');
    $youtubeUrl = config('services.youtube.channel_url');
@endphp


@if($preview)
    <div
        style="padding:10px 5%;background:#fff3dc;color:#633d10;text-align:center;font-size:12px;"
    >
        {{ $lang === 'bn'
            ? 'লোকাল ড্রাফট প্রিভিউ — অপ্রকাশিত ডেমো সংবাদ'
            : 'Local draft preview — unpublished demonstration stories'
        }}
    </div>
@endif


<!-- ===========================================================
     SECTION 10: MAIN THREE-COLUMN NEWS AREA
=========================================================== -->

<main class="main-container">

    <!-- LEFT COLUMN -->

    <section class="main-col-left">

        <!-- HERO STORY -->

        <div
            class="hero-card reveal"
            id="hero-section"
        >

            @if($hero)

                <div class="hero-banner">

                    <a href="{{ $postUrl($hero) }}">

                        <img
                            src="{{ $image($hero, $fallbackImage) }}"
                            alt="{{ $title($hero) }}"
                            fetchpriority="high"
                        >

                    </a>

                </div>

                <div class="hero-content">

                    <h1>

                        <a href="{{ $postUrl($hero) }}">
                            {{ $title($hero) }}
                        </a>

                    </h1>

                    <p>
                        {{ $summary($hero) }}
                    </p>

                    <a
                        href="{{ $postUrl($hero) }}"
                        class="hero-btn"
                        style="display:inline-block;"
                    >

                        {{ $lang === 'bn'
                            ? 'বিস্তারিত পড়ুন'
                            : 'Read Full Story'
                        }}

                    </a>

                </div>

            @else

                <div class="hero-content">

                    <h1>
                        {{ $lang === 'bn'
                            ? 'এখনো কোনো প্রকাশিত সংবাদ নেই'
                            : 'No published news available'
                        }}
                    </h1>

                </div>

            @endif

        </div>


        <!-- SUB STORIES -->

        <div class="sub-grid" id="sub-stories">

            @foreach($subStories as $news)

                <article class="sub-card reveal">

                    <a
                        href="{{ $postUrl($news) }}"
                        style="display:block;"
                    >

                        <img
                            src="{{ $image($news, $fallbackImage) }}"
                            alt="{{ $title($news) }}"
                            loading="lazy"
                            decoding="async"
                        >

                    </a>

                    <div class="sub-card-body">

                        <h3>

                            <a href="{{ $postUrl($news) }}">
                                {{ $title($news) }}
                            </a>

                        </h3>

                        <p>
                            {{ $summary($news) }}
                        </p>

                    </div>

                </article>

            @endforeach

        </div>


        <!-- DISCUSSED / TRENDING NEWS -->

        <div class="discussed-section reveal">

            <div
                class="section-title"
                data-bn="আলোচিত সংবাদ"
                data-en="Trending & Discussed News"
            >
                আলোচিত সংবাদ
            </div>


            <div
                class="discussed-grid"
                id="discussed-news-container"
            >

                @foreach($trendingPosts as $news)

                    <article class="discussed-item">

                        <a href="{{ $postUrl($news) }}">

                            <img
                                src="{{ $image($news, $fallbackImage) }}"
                                alt="{{ $title($news) }}"
                                loading="lazy"
                                decoding="async"
                            >

                        </a>

                        <div class="discussed-text">

                            <h4>

                                <a href="{{ $postUrl($news) }}">
                                    {{ $title($news) }}
                                </a>

                            </h4>

                            <p>
                                {{ $summary($news) }}
                            </p>

                        </div>

                    </article>

                @endforeach

            </div>

        </div>

    </section>


    <!-- MIDDLE COLUMN -->

    <div class="main-col-middle">

        <!-- LATEST UPDATES -->

        <aside
            class="sidebar reveal"
            id="side-feed"
        >

            <h3
                class="sidebar-title"
                data-bn="সর্বশেষ আপডেট"
                data-en="Latest Updates"
            >
                সর্বশেষ আপডেট
            </h3>


            <div id="side-feed-list">

                @foreach($latestPosts as $news)

                    <a
                        href="{{ $postUrl($news) }}"
                        class="feed-item"
                    >

                        <img
                            src="{{ $image($news, $fallbackImage) }}"
                            alt="{{ $title($news) }}"
                            loading="lazy"
                            decoding="async"
                        >

                        <div>

                            <h4>
                                {{ $title($news) }}
                            </h4>

                            <span class="feed-time">

                                <i class="fa fa-clock-o"></i>

                                {{ $news->published_at
                                    ? $news->published_at
                                        ->copy()
                                        ->locale($lang)
                                        ->diffForHumans()
                                    : ($lang === 'bn' ? 'খসড়া' : 'Draft')
                                }}

                            </span>

                        </div>

                    </a>

                @endforeach

            </div>

        </aside>


        <!-- MOST READ -->

        <aside
            class="sidebar sidebar-fill reveal"
            id="most-read-box"
        >

            <h3
                class="sidebar-title"
                style="color:var(--primary-red);"
                data-bn="সর্বাধিক পঠিত"
                data-en="Most Read News"
            >
                সর্বাধিক পঠিত
            </h3>


            <div id="most-read-list">

                @foreach($mostReadPosts as $news)

                    <a
                        href="{{ $postUrl($news) }}"
                        class="most-read-item"
                    >

                        <span class="most-read-rank">
                            {{ $bnNumber($loop->iteration) }}
                        </span>

                        <h4>
                            {{ $title($news) }}
                        </h4>

                    </a>

                @endforeach

            </div>

        </aside>

    </div>


    <!-- RIGHT COLUMN -->

    <aside class="main-col-right reveal">

        <!-- ADVERTISEMENT -->

        <div class="ad-slot ad-banner-box">

            <img
                src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=500&q=80"
                alt="Advertisement Banner"
                loading="lazy"
                decoding="async"
            >

            <div
                class="ad-overlay-text"
                data-bn="বিজ্ঞাপন এলাকা | স্পন্সরড"
                data-en="Advertisement Area | Sponsored"
            >
                বিজ্ঞাপন এলাকা | স্পন্সরড
            </div>

        </div>


        <!-- PROMOTIONAL BANNER -->

        <div class="ad-slot ad-promo-box">

            <img
                src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500&q=80"
                alt="Promotional Campaign Banner"
                loading="lazy"
                decoding="async"
            >

            <div
                class="ad-overlay-text"
                data-bn="প্রোমোশনাল ব্যানার | ব্যবসার প্রসারে বিজ্ঞাপন দিন"
                data-en="Promotional Banner | Advertise With Us"
            >
                প্রোমোশনাল ব্যানার | ব্যবসার প্রসারে বিজ্ঞাপন দিন
            </div>

        </div>


        <!-- DYNAMIC EPAPER LINK -->

        <div class="right-info-card">

            <div
                class="right-info-header"
                data-bn="আজকের ই-পেপার"
                data-en="Today's E-Paper"
            >
                আজকের ই-পেপার
            </div>


            <div class="right-info-body">

                <a href="{{ $epaperLink }}">

                    <img
                        src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=400&q=80"
                        alt="Daily Dariyanagar E-Paper"
                        loading="lazy"
                        decoding="async"
                    >

                </a>


                <p
                    data-bn="ডেইলি দরিয়ানগরের ডিজিটাল সংবাদপত্র পড়ুন এবং পিডিএফ ডাউনলোড করুন।"
                    data-en="Read the Daily Dariyanagar digital newspaper and download its PDF."
                >
                    ডেইলি দরিয়ানগরের ডিজিটাল সংবাদপত্র পড়ুন এবং পিডিএফ ডাউনলোড করুন।
                </p>


                <a
                    href="{{ $epaperLink }}"
                    class="epaper-card-btn"
                    data-bn="ই-পেপার পড়ুন"
                    data-en="Read E-Paper Now"
                >
                    ই-পেপার পড়ুন
                </a>

            </div>

        </div>


        <!-- PRAYER & WEATHER WIDGET -->
        <!-- Original values retained as static display content. -->

        <div class="right-info-card right-card-fill">

            <div
                class="right-info-header bg-green-header"
                data-bn="নামাজের সময়সূচি (চট্টগ্রাম)"
                data-en="Prayer Schedule (Chattogram)"
            >
                নামাজের সময়সূচি (চট্টগ্রাম)
            </div>

            <div class="right-info-body">

                <ul class="prayer-list">

                    <li>
                        <span data-bn="ফজর" data-en="Fajr">ফজর</span>
                        <strong data-bn="ভোর ৫:০৪ মি." data-en="5:04 AM">ভোর ৫:০৪ মি.</strong>
                    </li>

                    <li>
                        <span data-bn="জোহর" data-en="Dhuhr">জোহর</span>
                        <strong data-bn="দুপুর ১২:১০ মি." data-en="12:10 PM">দুপুর ১২:১০ মি.</strong>
                    </li>

                    <li>
                        <span data-bn="আসর" data-en="Asr">আসর</span>
                        <strong data-bn="বিকেল ৪:১৫ মি." data-en="4:15 PM">বিকেল ৪:১৫ মি.</strong>
                    </li>

                    <li>
                        <span data-bn="মাগরিব" data-en="Maghrib">মাগরিব</span>
                        <strong data-bn="সন্ধ্যা ৫:৫৮ মি." data-en="5:58 PM">সন্ধ্যা ৫:৫৮ মি.</strong>
                    </li>

                    <li>
                        <span data-bn="এশা" data-en="Isha">এশা</span>
                        <strong data-bn="রাত ৭:১৫ মি." data-en="7:15 PM">রাত ৭:১৫ মি.</strong>
                    </li>

                </ul>


                <div class="weather-mini-box">

                    <i class="fa fa-sun-o"></i>

                    <span
                        data-bn="চট্টগ্রামের আবহাওয়া: ২৮° সে. | রৌদ্রোজ্জ্বল"
                        data-en="Chattogram Weather: 28°C | Sunny"
                    >
                        চট্টগ্রামের আবহাওয়া: ২৮° সে. | রৌদ্রোজ্জ্বল
                    </span>

                </div>

            </div>

        </div>

    </aside>

</main>


<!-- ===========================================================
     SECTION 11: FEATURED AND INVESTIGATIVE REPORTS
=========================================================== -->

<div class="section-two-container reveal">

    <!-- LEFT CONTENT -->

    <div>

        <!-- SIX FEATURED CARDS -->

        <div class="grid-3-col">

            @foreach($featuredCards as $news)

                <article
                    class="img-card {{ $loop->index >= 3 ? 'bg-green' : '' }}"
                >

                    <div class="img-wrap">

                        <a href="{{ $postUrl($news) }}">

                            <img
                                src="{{ $image($news, $fallbackImage) }}"
                                alt="{{ $title($news) }}"
                                loading="lazy"
                                decoding="async"
                            >

                        </a>

                    </div>


                    <div class="img-card-title">

                        <h4>

                            <a href="{{ $postUrl($news) }}">
                                {{ $title($news) }}
                            </a>

                        </h4>

                        <p class="card-extra-desc">
                            {{ $summary($news) }}
                        </p>

                    </div>

                </article>

            @endforeach

        </div>


        <!-- SPECIAL REPORTS -->

        <div
            class="discussed-header-custom"
            data-bn="আলোচিত ও বিশেষ প্রতিবেদন"
            data-en="Featured & In-Depth Reports"
        >
            আলোচিত ও বিশেষ প্রতিবেদন
        </div>


        <div class="discussed-grid-2">

            @foreach($deepReports as $news)

                <article class="discussed-article">

                    <a href="{{ $postUrl($news) }}">

                        <img
                            src="{{ $image($news, $fallbackImage) }}"
                            alt="{{ $title($news) }}"
                            loading="lazy"
                            decoding="async"
                        >

                    </a>


                    <div class="discussed-article-content">

                        <h4>

                            <a href="{{ $postUrl($news) }}">
                                {{ $title($news) }}
                            </a>

                        </h4>


                        <p>

                            <span>
                                {{ $summary($news) }}
                            </span>

                            <a
                                href="{{ $postUrl($news) }}"
                                data-bn="বিস্তারিত"
                                data-en="Read More"
                            >
                                বিস্তারিত
                            </a>

                        </p>

                    </div>

                </article>

            @endforeach

        </div>

    </div>


    <!-- RIGHT SOCIAL / INVESTIGATION COLUMN -->

    <div>

        <div class="sidebar-widget">

            <div
                class="sidebar-widget-header"
                data-bn="ফেসবুকে আমরা"
                data-en="Find Us on Facebook"
            >
                ফেসবুকে আমরা
            </div>


            <div class="fb-placeholder">

                <div
                    style="display:flex;align-items:center;gap:12px;margin-bottom:15px;justify-content:center;"
                >

                    <img
                        src="{{ asset('frontend/img/logo.png') }}"
                        style="border-radius:50%;width:45px;height:45px;object-fit:cover;border:1px solid #ddd;"
                        alt="Daily Dariyanagar"
                        loading="lazy"
                    >

                    <div style="text-align:left;">

                        <strong
                            style="color:#3b5998;font-size:0.95rem;"
                        >
                            Daily Dariyanagar
                        </strong>

                    </div>

                </div>


                @if(filled($facebookUrl))

                    <a
                        href="{{ $facebookUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        style="display:inline-block;background:#f0f2f5;border:1px solid #ccd0d5;padding:6px 15px;font-weight:bold;font-size:0.85rem;border-radius:4px;"
                    >

                        <i
                            class="fa fa-facebook-square"
                            style="color:#3b5998;"
                        ></i>

                        <span data-bn="পেজ ফলো করুন" data-en="Follow Page">
                            পেজ ফলো করুন
                        </span>

                    </a>

                @else

                    <span
                        style="display:inline-block;background:#f0f2f5;border:1px solid #ccd0d5;padding:6px 15px;font-weight:bold;font-size:0.85rem;border-radius:4px;"
                    >
                        <i class="fa fa-facebook-square"></i>
                        Daily Dariyanagar
                    </span>

                @endif

            </div>


            <!-- YOUTUBE -->

            @if(filled($youtubeUrl))

                <a
                    href="{{ $youtubeUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="yt-placeholder"
                    style="display:block;"
                >

                    <i
                        class="fa fa-youtube-play"
                        style="font-size:3.5rem;margin-bottom:5px;"
                    ></i>

                    <span
                        data-bn="আমাদের ইউটিউব চ্যানেল সাবস্ক্রাইব করুন"
                        data-en="Subscribe to Our YouTube Channel"
                    >
                        আমাদের ইউটিউব চ্যানেল সাবস্ক্রাইব করুন
                    </span>

                </a>

            @else

                <div class="yt-placeholder">

                    <i
                        class="fa fa-youtube-play"
                        style="font-size:3.5rem;margin-bottom:5px;"
                    ></i>

                    <span
                        data-bn="আমাদের ইউটিউব চ্যানেল"
                        data-en="Our YouTube Channel"
                    >
                        আমাদের ইউটিউব চ্যানেল
                    </span>

                </div>

            @endif


            <!-- INVESTIGATION -->

            <div class="anusondhan-header">

                <a href="{{ $categoryLink('investigation') }}">

                    {{ $lang === 'bn'
                        ? 'অনুসন্ধান সেল'
                        : 'Investigative Desk'
                    }}

                </a>

            </div>


            <div class="anusondhan-content">

                @if($investigationLead)

                    <a
                        class="anusondhan-featured"
                        href="{{ $postUrl($investigationLead) }}"
                    >

                        <img
                            src="{{ $image($investigationLead, $fallbackImage) }}"
                            alt="{{ $title($investigationLead) }}"
                            loading="lazy"
                            decoding="async"
                        >

                        <div>

                            <h4>
                                {{ $title($investigationLead) }}
                            </h4>

                        </div>

                    </a>


                    <p
                        style="font-size:0.8rem;color:#555;margin-bottom:10px;line-height:1.5;"
                    >

                        {{ $summary($investigationLead) }}

                        <a
                            href="{{ $postUrl($investigationLead) }}"
                            style="color:var(--primary-red);font-weight:600;"
                        >
                            {{ $lang === 'bn' ? 'বিস্তারিত' : 'Read More' }}
                        </a>

                    </p>

                @endif


                <ul class="anusondhan-list">

                    @foreach($investigationOthers as $news)

                        <li>

                            <a href="{{ $postUrl($news) }}">
                                {{ $title($news) }}
                            </a>

                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

</div>


<!-- ===========================================================
     SECTION 12: THREE-COLUMN CATEGORY AREA
=========================================================== -->

<div class="news-3col-wrapper reveal">

    @foreach($threeColumns as $column)

        <div class="news-3col-column">

            @foreach($column as $slug)

                @php
                    $stories = $section($slug);
                @endphp

                <div
                    class="news-3col-category"
                    id="category-{{ $slug }}"
                >

                    <div
                        class="news-3col-header {{ $slug === 'sports' ? 'no-border' : '' }}"
                    >

                        <a
                            href="{{ $categoryLink($slug) }}"
                            style="display:block;"
                        >
                            {{ $label($slug) }}
                        </a>

                    </div>


                    @if($slug === 'sports')

                        @if($stories->isNotEmpty())

                            @php
                                $lead = $stories->first();
                            @endphp

                            <a
                                href="{{ $postUrl($lead) }}"
                                class="news-3col-block-item"
                                style="display:block;"
                            >

                                <img
                                    src="{{ $image($lead, $fallbackImage) }}"
                                    alt="{{ $title($lead) }}"
                                    loading="lazy"
                                    decoding="async"
                                >

                                <h4>
                                    {{ $title($lead) }}
                                </h4>

                                <p
                                    style="font-size:0.82rem;color:#555;margin-top:6px;line-height:1.45;"
                                >
                                    {{ $summary($lead) }}
                                </p>

                            </a>

                        @endif


                        <ul class="news-3col-bullet-list">

                            @foreach($stories->skip(1)->take(10) as $news)

                                <li>

                                    <a href="{{ $postUrl($news) }}">
                                        {{ $title($news) }}
                                    </a>

                                </li>

                            @endforeach

                        </ul>


                    @else

                        @foreach($stories->take(2) as $news)

                            <article class="news-3col-item">

                                <a href="{{ $postUrl($news) }}">

                                    <img
                                        src="{{ $image($news, $fallbackImage) }}"
                                        alt="{{ $title($news) }}"
                                        loading="lazy"
                                        decoding="async"
                                    >

                                </a>


                                <div class="news-3col-item-content">

                                    <h4>

                                        <a href="{{ $postUrl($news) }}">
                                            {{ $title($news) }}
                                        </a>

                                    </h4>


                                    <p>

                                        {{ $summary($news) }}

                                        <a href="{{ $postUrl($news) }}">

                                            {{ $lang === 'bn'
                                                ? 'বিস্তারিত'
                                                : 'Read More'
                                            }}

                                        </a>

                                    </p>

                                </div>

                            </article>

                        @endforeach


                        <ul class="news-3col-bullet-list">

                            @foreach($stories->skip(2)->take(5) as $news)

                                <li>

                                    <a href="{{ $postUrl($news) }}">
                                        {{ $title($news) }}
                                    </a>

                                </li>

                            @endforeach

                        </ul>

                    @endif

                </div>

            @endforeach

        </div>

    @endforeach

</div>


<!-- ===========================================================
     SECTION 13: FIVE-COLUMN CATEGORY GRID
=========================================================== -->

<div class="image-row-grid reveal">

    @foreach($fiveCategories as $slug)

        @php
            $stories = $section($slug);
        @endphp

        <div
            class="category-block"
            id="category-{{ $slug }}"
        >

            <div class="category-header">

                <a
                    href="{{ $categoryLink($slug) }}"
                    style="display:block;"
                >
                    {{ $label($slug) }}
                </a>

            </div>


            <div class="cat-news-wrapper">

                @if($stories->isNotEmpty())

                    @php
                        $lead = $stories->first();
                    @endphp

                    <a
                        class="cat-main-story"
                        href="{{ $postUrl($lead) }}"
                    >

                        <img
                            src="{{ $image($lead, $fallbackImage) }}"
                            alt="{{ $title($lead) }}"
                            loading="lazy"
                            decoding="async"
                        >

                        <h4>
                            {{ $title($lead) }}
                        </h4>

                    </a>

                @endif


                <ul class="cat-list">

                    @foreach($stories->skip(1)->take(3) as $news)

                        <li>

                            <a href="{{ $postUrl($news) }}">
                                {{ $title($news) }}
                            </a>

                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endforeach

</div>


<!-- ===========================================================
     SECTION 14: PHOTO GALLERY
=========================================================== -->

<div
    class="photo-gallery-section reveal"
    id="category-photo-gallery"
>

    <a
        href="{{ $categoryLink('photo-gallery') }}"
        class="gallery-header"
    >
        {{ $lang === 'bn' ? 'ছবিঘর' : 'Photo Gallery' }}
    </a>


    <div class="gallery-grid">

        @foreach($section('photo-gallery')->take(12) as $news)

            <a
                href="{{ $postUrl($news) }}"
                class="gallery-item"
            >

                <img
                    src="{{ $image($news, $fallbackImage) }}"
                    alt="{{ $title($news) }}"
                    loading="lazy"
                    decoding="async"
                >

                <p>
                    {{ $title($news) }}
                </p>

            </a>

        @endforeach

    </div>

</div>


<!-- ===========================================================
     SECTION 15: FOUR RED CATEGORY BLOCKS
=========================================================== -->

<div class="four-column-grid reveal">

    @foreach($redCategories as $slug)

        @php
            $stories = $section($slug);
        @endphp

        <div
            class="red-block"
            id="category-{{ $slug }}"
        >

            <div class="red-header">

                <a
                    href="{{ $categoryLink($slug) }}"
                    style="display:block;color:inherit;"
                >
                    {{ $label($slug) }}
                </a>

            </div>


            <div class="red-content">

                @if($stories->isNotEmpty())

                    @php
                        $lead = $stories->first();
                    @endphp

                    <a href="{{ $postUrl($lead) }}">

                        <img
                            src="{{ $image($lead, $fallbackImage) }}"
                            alt="{{ $title($lead) }}"
                            loading="lazy"
                            decoding="async"
                        >

                        <h4>
                            {{ $title($lead) }}
                        </h4>

                    </a>


                    <p class="red-desc">
                        {{ $summary($lead) }}
                    </p>

                @endif


                <ul class="cat-list">

                    @foreach($stories->skip(1)->take(2) as $news)

                        <li>

                            <a href="{{ $postUrl($news) }}">
                                {{ $title($news) }}
                            </a>

                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endforeach

</div>


<!-- ===========================================================
     SECTION 16: HEALTH & WELLNESS
=========================================================== -->

<div
    class="health-section reveal"
    id="category-health"
>

    <a
        href="{{ $categoryLink('health') }}"
        class="health-header"
    >

        {{ $lang === 'bn'
            ? 'স্বাস্থ্য ও চিকিৎসা'
            : 'Health & Wellness'
        }}

    </a>


    <div class="health-grid">

        @foreach($section('health')->take(5) as $news)

            <a
                href="{{ $postUrl($news) }}"
                class="health-item"
            >

                <img
                    src="{{ $image($news, $fallbackImage) }}"
                    alt="{{ $title($news) }}"
                    loading="lazy"
                    decoding="async"
                >

                <p>
                    {{ $title($news) }}
                </p>

            </a>

        @endforeach

    </div>

</div>


<!-- ===========================================================
     SECTION 17: EXISTING FOOTER
=========================================================== -->

@include('home.footer')


<!-- ===========================================================
     PAGE DATA FOR THE EXISTING HEADER
=========================================================== -->

<script>
    window.DARIYANAGAR_HOME = {{ \Illuminate\Support\Js::from($readerData) }};
</script>


<!-- ===========================================================
     PAGE-LEVEL INTERACTIONS
     No changes to public/frontend/js/script.js required.

     The old script.js is not loaded here because it overwrites
     Filament articles with hardcoded sample headlines and links.
=========================================================== -->

<script>
(function () {
    "use strict";

    const data = window.DARIYANAGAR_HOME || {};

    const lang = data.lang === "en" ? "en" : "bn";

    const epaperUrl = data.epaperUrl || "/epaper";

    function updateLabels() {
        document.documentElement.lang = lang;

        document.querySelectorAll("[data-bn][data-en]")
            .forEach(function (element) {
                element.textContent =
                    element.getAttribute("data-" + lang) || "";
            });

        const toggle = document.getElementById("lang-toggle");

        if (toggle) {
            toggle.textContent =
                lang === "bn" ? "English" : "বাংলা";
        }

        const search = document.getElementById("search-input");

        if (search) {
            search.placeholder =
                lang === "bn" ? "খবর খুঁজুন..." : "Search news...";

            search.value = data.search || "";
        }
    }

    function renderNavigation() {
        const nav = document.getElementById("nav-list");

        if (!nav) return;

        nav.replaceChildren();

        const homeLink = document.createElement("a");

        const homeUrl = new URL(
            data.homeUrl || "/",
            window.location.origin
        );

        if (lang === "en") {
            homeUrl.searchParams.set("lang", "en");
        }

        if (data.preview) {
            homeUrl.searchParams.set("preview", "1");
        }

        homeLink.href = homeUrl.toString();
        homeLink.textContent = lang === "bn" ? "প্রচ্ছদ" : "Home";

        nav.appendChild(homeLink);

        (data.navigation || []).forEach(function (category) {
            const link = document.createElement("a");

            link.href = category.url;
            link.textContent =
                lang === "bn" ? category.bn : category.en;

            nav.appendChild(link);
        });

        const epaper = document.createElement("a");

        epaper.href = epaperUrl;
        epaper.textContent =
            lang === "bn" ? "ই-পেপার" : "E-Paper";

        nav.appendChild(epaper);
    }

    function renderTicker() {
        const marquee = document.getElementById(
            "breaking-marquee"
        );

        if (!marquee) return;

        marquee.replaceChildren();

        (data.ticker || []).forEach(function (item, index) {
            if (index > 0) {
                marquee.appendChild(
                    document.createTextNode("   •   ")
                );
            }

            const link = document.createElement("a");

            link.href = item.url;

            link.textContent =
                lang === "bn" ? item.bn : item.en;

            marquee.appendChild(link);
        });
    }

    function fixHeaderEpaperLinks() {
        document.querySelectorAll(".epaper-btn")
            .forEach(function (element) {
                if (element.tagName === "A") {
                    element.setAttribute("href", epaperUrl);
                } else {
                    element.addEventListener("click", function () {
                        window.location.assign(epaperUrl);
                    });
                }
            });
    }

    function updateClock() {
        const element = document.getElementById("live-clock");

        if (!element) return;

        element.textContent = new Intl.DateTimeFormat(
            lang === "bn" ? "bn-BD" : "en-US",
            {
                weekday: "long",
                day: "numeric",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: true,
                timeZone: "Asia/Dhaka"
            }
        ).format(new Date());
    }

    function setupLanguage() {
        document.getElementById("lang-toggle")
            ?.addEventListener("click", function () {
                const url = new URL(window.location.href);

                if (lang === "bn") {
                    url.searchParams.set("lang", "en");
                } else {
                    url.searchParams.delete("lang");
                }

                window.location.assign(url.toString());
            });
    }

    function setupSearch() {
        const input = document.getElementById("search-input");

        if (!input) return;

        function search() {
            const url = new URL(
                data.homeUrl || "/",
                window.location.origin
            );

            const term = input.value.trim();

            if (term) {
                url.searchParams.set("q", term);
            }

            if (lang === "en") {
                url.searchParams.set("lang", "en");
            }

            if (data.preview) {
                url.searchParams.set("preview", "1");
            }

            window.location.assign(url.toString());
        }

        const form = input.closest("form");

        if (form) {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                search();
            });
        } else {
            input.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    search();
                }
            });

            document.querySelector(".search-box button")
                ?.addEventListener("click", function (event) {
                    event.preventDefault();
                    search();
                });
        }
    }

    function setupReveals() {
        const elements = document.querySelectorAll(".reveal");

        if (!("IntersectionObserver" in window)) {
            elements.forEach(function (item) {
                item.classList.add("active");
            });

            return;
        }

        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.08 }
        );

        elements.forEach(function (item) {
            observer.observe(item);
        });
    }

    function setupScrollTop() {
        const button =
            document.getElementById("scrollTopBtn") ||
            document.querySelector(".scroll-top");

        if (!button) return;

        function refresh() {
            button.style.display =
                window.scrollY > 350 ? "flex" : "none";
        }

        button.addEventListener("click", function () {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        window.addEventListener("scroll", refresh, {
            passive: true
        });

        refresh();
    }

    function initialize() {
        updateLabels();
        renderNavigation();
        renderTicker();
        fixHeaderEpaperLinks();
        updateClock();
        setupLanguage();
        setupSearch();
        setupReveals();
        setupScrollTop();

        window.setInterval(updateClock, 1000);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initialize);
    } else {
        initialize();
    }
})();
</script>

</body>
</html>
