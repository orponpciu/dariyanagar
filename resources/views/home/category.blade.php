<!DOCTYPE html>
<html lang="{{ $lang }}">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="theme-color" content="#d32f2f">

    <title>{{ $seoTitle }}</title>

    <meta
        name="description"
        content="{{ $seoDescription }}"
    >

    <meta name="robots" content="{{ $robots }}">

    <link rel="canonical" href="{{ $canonical }}">

    <link
        rel="alternate"
        hreflang="bn-BD"
        href="{{ route('category.show', ['slug' => $category->slug]) }}"
    >

    <link
        rel="alternate"
        hreflang="en"
        href="{{ route('category.show', ['slug' => $category->slug, 'lang' => 'en']) }}"
    >

    <link
        rel="alternate"
        hreflang="x-default"
        href="{{ route('category.show', ['slug' => $category->slug]) }}"
    >

    <!-- OPEN GRAPH -->

    <meta property="og:type" content="website">

    <meta
        property="og:site_name"
        content="Daily Dariyanagar"
    >

    <meta property="og:title" content="{{ $ogTitle }}">

    <meta
        property="og:description"
        content="{{ $ogDescription }}"
    >

    <meta property="og:url" content="{{ $canonical }}">

    <meta
        property="og:locale"
        content="{{ $lang === 'bn' ? 'bn_BD' : 'en_US' }}"
    >

    <meta
        property="og:image"
        content="{{ $imageUrl($category->og_image ?: $category->image) }}"
    >

    <!-- TWITTER / X -->

    <meta name="twitter:card" content="summary_large_image">

    <meta
        name="twitter:title"
        content="{{ $category->{'twitter_title_' . $lang} ?: $ogTitle }}"
    >

    <meta
        name="twitter:description"
        content="{{ $category->{'twitter_description_' . $lang} ?: $ogDescription }}"
    >

    <meta
        name="twitter:image"
        content="{{ $imageUrl($category->og_image ?: $category->image) }}"
    >

    <!-- ORIGINAL HOMEPAGE THEME -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('frontend/css/style.css') }}"
    >

    <!-- CATEGORY PAGE STYLES ONLY -->

    <link
        rel="stylesheet"
        href="{{ asset('frontend/css/category.css') }}"
    >

    <!-- STRUCTURED DATA -->

    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',

            'name' => $categoryName,

            'description' => $seoDescription,

            'url' => $canonical,

            'inLanguage' => $lang === 'bn'
                ? 'bn-BD'
                : 'en-US',

            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Daily Dariyanagar',
                'url' => route('home'),
            ],
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode(
            $schema,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        ) !!}
    </script>

</head>

<body>

<!-- ORIGINAL WEBSITE HEADER -->

@include('home.header')


<!-- CATEGORY PAGE -->

<main class="category-page">

    <!-- BREADCRUMBS -->

    <div class="category-breadcrumb reveal active">

        <a href="{{ route('home', $lang === 'en' ? ['lang' => 'en'] : []) }}">
            {{ $lang === 'bn' ? 'প্রচ্ছদ' : 'Home' }}
        </a>

        <i
            class="fa fa-angle-right"
            aria-hidden="true"
        ></i>

        <span aria-current="page">
            {{ $categoryName }}
        </span>

    </div>


    <!-- AUTHENTICATED LOCAL DRAFT PREVIEW -->

    @if($preview)

        <p
            class="category-preview-notice"
            role="status"
        >
            {{ $lang === 'bn'
                ? 'শুধু লোকাল প্রিভিউ: এখানে অপ্রকাশিত খসড়াও দেখা যাচ্ছে। এগুলো যাচাইকৃত প্রকাশিত সংবাদ নয়।'
                : 'Local preview only: unpublished drafts are visible. These are not verified, published news.'
            }}
        </p>

    @endif


    <!-- MAIN TWO-COLUMN CATEGORY LAYOUT -->

    <div class="category-page-layout">

        <!-- LEFT: CATEGORY NEWS -->

        <section
            class="category-main"
            aria-labelledby="category-title"
        >

            <!-- CATEGORY HEADING -->

            <div class="news-3col-header category-main-heading">

                <h1 id="category-title">
                    {{ $categoryName }}
                </h1>

            </div>


            <!-- CATEGORY DESCRIPTION -->

            @if(filled($categoryDescription))

                <p class="category-description">
                    {{ $categoryDescription }}
                </p>

            @endif


            <!-- TOTAL ARTICLES + SORTING -->

            <div class="category-toolbar">

                <span class="category-total">

                    {{ $lang === 'bn'
                        ? 'মোট সংবাদ:'
                        : 'Total articles:'
                    }}

                    <strong>
                        {{ $posts->total() }}
                    </strong>

                </span>


                <form
                    method="GET"
                    action="{{ route('category.show', ['slug' => $category->slug]) }}"
                    class="category-sort-form"
                >

                    @if($lang === 'en')

                        <input
                            type="hidden"
                            name="lang"
                            value="en"
                        >

                    @endif


                    @if($preview)

                        <input
                            type="hidden"
                            name="preview"
                            value="1"
                        >

                    @endif


                    <label for="category-sort">

                        {{ $lang === 'bn'
                            ? 'সাজান:'
                            : 'Sort by:'
                        }}

                    </label>


                    <select
                        name="sort"
                        id="category-sort"
                    >

                        <option
                            value="latest"
                            @selected($sort === 'latest')
                        >
                            {{ $lang === 'bn'
                                ? 'সর্বশেষ'
                                : 'Latest'
                            }}
                        </option>

                        <option
                            value="popular"
                            @selected($sort === 'popular')
                        >
                            {{ $lang === 'bn'
                                ? 'সর্বাধিক পঠিত'
                                : 'Most Read'
                            }}
                        </option>

                        <option
                            value="oldest"
                            @selected($sort === 'oldest')
                        >
                            {{ $lang === 'bn'
                                ? 'পুরোনো আগে'
                                : 'Oldest'
                            }}
                        </option>

                    </select>


                    <button
                        type="submit"
                        class="category-sort-submit"
                    >
                        {{ $lang === 'bn'
                            ? 'দেখুন'
                            : 'Apply'
                        }}
                    </button>

                </form>

            </div>


            <!-- CATEGORY FEATURED ARTICLE -->

            @if($lead)

                <article class="category-lead-card img-card reveal">

                    <a
                        href="{{ $postUrl($lead) }}"
                        class="category-lead-photo"
                    >

                        <img
                            src="{{ $imageUrl($lead->featured_image) }}"
                            alt="{{ $lead->{'image_alt_' . $lang} ?: $title($lead) }}"
                            fetchpriority="high"
                        >

                    </a>


                    <div class="img-card-title category-lead-body">

                        <div class="category-story-info">

                            <span>{{ $categoryName }}</span>

                            <span aria-hidden="true">•</span>

                            <time
                                @if($lead->published_at)
                                    datetime="{{ $lead->published_at->toIso8601String() }}"
                                @endif
                            >

                                {{ $lead->published_at
                                    ? $lead->published_at
                                        ->copy()
                                        ->locale($lang)
                                        ->translatedFormat('d F Y')
                                    : ($lang === 'bn' ? 'খসড়া' : 'Draft')
                                }}

                            </time>

                        </div>


                        <h2>

                            <a href="{{ $postUrl($lead) }}">
                                {{ $title($lead) }}
                            </a>

                        </h2>


                        <p>
                            {{ $summary($lead) }}
                        </p>


                        <a
                            href="{{ $postUrl($lead) }}"
                            class="category-read-more"
                        >

                            {{ $lang === 'bn'
                                ? 'বিস্তারিত পড়ুন'
                                : 'Read Full Story'
                            }}

                            <i
                                class="fa fa-angle-right"
                                aria-hidden="true"
                            ></i>

                        </a>

                    </div>

                </article>

            @endif


            <!-- CATEGORY NEWS GRID -->

            <div class="category-story-grid">

                @foreach($listPosts as $news)

                    <article class="sub-card category-story-card reveal">

                        <a
                            href="{{ $postUrl($news) }}"
                            class="category-story-photo"
                        >

                            <img
                                src="{{ $imageUrl($news->featured_image) }}"
                                alt="{{ $news->{'image_alt_' . $lang} ?: $title($news) }}"
                                loading="lazy"
                                decoding="async"
                            >

                        </a>


                        <div class="sub-card-body">

                            <div class="category-story-info">

                                <time
                                    @if($news->published_at)
                                        datetime="{{ $news->published_at->toIso8601String() }}"
                                    @endif
                                >

                                    <i
                                        class="fa fa-clock-o"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $news->published_at
                                        ? $news->published_at
                                            ->copy()
                                            ->locale($lang)
                                            ->diffForHumans()
                                        : ($lang === 'bn' ? 'খসড়া' : 'Draft')
                                    }}

                                </time>

                            </div>


                            <h3>

                                <a href="{{ $postUrl($news) }}">
                                    {{ $title($news) }}
                                </a>

                            </h3>


                            <p>
                                {{ $summary($news) }}
                            </p>


                            <a
                                href="{{ $postUrl($news) }}"
                                class="category-card-link"
                            >

                                {{ $lang === 'bn'
                                    ? 'বিস্তারিত'
                                    : 'Read More'
                                }}

                            </a>

                        </div>

                    </article>

                @endforeach

            </div>


            <!-- EMPTY CATEGORY -->

            @if($posts->isEmpty())

                <div class="category-empty sidebar">

                    <h2>

                        {{ $lang === 'bn'
                            ? 'এই বিভাগে এখনো প্রকাশিত সংবাদ নেই'
                            : 'No articles in this category yet'
                        }}

                    </h2>

                    <p>

                        {{ $lang === 'bn'
                            ? 'নতুন সংবাদ প্রকাশিত হলে এখানে দেখা যাবে।'
                            : 'New articles will appear here after publication.'
                        }}

                    </p>

                </div>

            @endif


            <!-- DATABASE PAGINATION -->

            @if($posts->hasPages())

                <nav
                    class="category-pagination"
                    aria-label="{{ $lang === 'bn' ? 'পাতা নির্বাচন' : 'Pagination' }}"
                >

                    @if($posts->onFirstPage())

                        <span class="category-page-disabled">

                            {{ $lang === 'bn'
                                ? 'আগের'
                                : 'Previous'
                            }}

                        </span>

                    @else

                        <a
                            href="{{ $posts->previousPageUrl() }}"
                            rel="prev"
                        >

                            {{ $lang === 'bn'
                                ? 'আগের'
                                : 'Previous'
                            }}

                        </a>

                    @endif


                    @foreach(
                        $posts->getUrlRange(
                            max(1, $posts->currentPage() - 2),
                            min($posts->lastPage(), $posts->currentPage() + 2)
                        ) as $page => $url
                    )

                        <a
                            href="{{ $url }}"
                            @if($page === $posts->currentPage())
                                aria-current="page"
                                class="category-page-current"
                            @endif
                        >
                            {{ $page }}
                        </a>

                    @endforeach


                    @if($posts->hasMorePages())

                        <a
                            href="{{ $posts->nextPageUrl() }}"
                            rel="next"
                        >

                            {{ $lang === 'bn'
                                ? 'পরের'
                                : 'Next'
                            }}

                        </a>

                    @else

                        <span class="category-page-disabled">

                            {{ $lang === 'bn'
                                ? 'পরের'
                                : 'Next'
                            }}

                        </span>

                    @endif

                </nav>

            @endif

        </section>


        <!-- RIGHT: ORIGINAL SIDEBAR STYLING -->

        <aside
            class="category-sidebar"
            aria-label="{{ $lang === 'bn' ? 'সংবাদ সাইডবার' : 'News sidebar' }}"
        >

            <!-- ALL CATEGORIES -->

            <div class="sidebar reveal">

                <h3 class="sidebar-title">

                    {{ $lang === 'bn'
                        ? 'সব বিভাগ'
                        : 'All Categories'
                    }}

                </h3>


                <div class="category-sidebar-links">

                    @foreach($categories as $item)

                        <a
                            href="{{ $categoryUrl($item) }}"
                            @class([
                                'category-active' => $item->id === $category->id
                            ])
                        >

                            {{ $item->{'name_' . $lang} ?: $item->name_bn }}

                        </a>

                    @endforeach

                </div>

            </div>


            <!-- LATEST NEWS -->

            <div class="sidebar reveal">

                <h3 class="sidebar-title">

                    {{ $lang === 'bn'
                        ? 'সর্বশেষ আপডেট'
                        : 'Latest Updates'
                    }}

                </h3>


                @foreach($latestPosts as $news)

                    <a
                        class="feed-item"
                        href="{{ $postUrl($news) }}"
                    >

                        <img
                            src="{{ $imageUrl($news->featured_image) }}"
                            alt="{{ $title($news) }}"
                            loading="lazy"
                        >

                        <div>

                            <h4>
                                {{ $title($news) }}
                            </h4>

                            <span class="feed-time">

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


            <!-- MOST READ NEWS -->

            <div class="sidebar reveal">

                <h3
                    class="sidebar-title"
                    style="color:var(--primary-red);"
                >

                    {{ $lang === 'bn'
                        ? 'সর্বাধিক পঠিত'
                        : 'Most Read News'
                    }}

                </h3>


                @foreach($mostReadPosts as $news)

                    <a
                        class="most-read-item"
                        href="{{ $postUrl($news) }}"
                    >

                        <span class="most-read-rank">
                            {{ $loop->iteration }}
                        </span>

                        <h4>
                            {{ $title($news) }}
                        </h4>

                    </a>

                @endforeach

            </div>


            <!-- ORIGINAL ADVERTISEMENT STYLE -->

            <div class="ad-slot ad-banner-box reveal">

                <img
                    src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=500&q=80"
                    alt="Advertisement"
                    loading="lazy"
                >

                <div
                    class="ad-overlay-text"
                    data-bn="বিজ্ঞাপন এলাকা | স্পন্সরড"
                    data-en="Advertisement Area | Sponsored"
                >
                    বিজ্ঞাপন এলাকা | স্পন্সরড
                </div>

            </div>

        </aside>

    </div>

</main>


<!-- EXISTING WEBSITE FOOTER -->

@include('home.footer')


<!-- CATEGORY BACKEND DATA FOR JAVASCRIPT -->

<script>
    window.DARIYANAGAR_CATEGORY = {{ \Illuminate\Support\Js::from([
        'lang' => $lang,
        'preview' => $preview,
        'homeUrl' => route('home'),
        'navigation' => $navigation,
        'ticker' => $ticker,
    ]) }};
</script>


<script
    src="{{ asset('frontend/js/category.js') }}"
    defer
></script>

</body>
</html>