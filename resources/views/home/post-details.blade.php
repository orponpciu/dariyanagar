<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <!-- CHARACTER ENCODING & VIEWPORT -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- DYNAMIC SEO -->
    <title>{{ $seoTitle }}</title>
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">

    @if(filled($focusKeyword))
        <meta name="keywords" content="{{ $focusKeyword }}">
    @endif

    <meta name="author" content="{{ $reporter }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#d32f2f">
    <link rel="canonical" href="{{ $canonical }}">

    <!-- LANGUAGE ALTERNATES -->
    <link rel="alternate" hreflang="bn-BD" href="{{ $bengaliUrl }}">
    <link rel="alternate" hreflang="en" href="{{ $englishUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $bengaliUrl }}">

    <!-- OPEN GRAPH -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $lang === 'en' ? $englishUrl : $articleUrl }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:locale" content="{{ $lang === 'bn' ? 'bn_BD' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ $lang === 'bn' ? 'en_US' : 'bn_BD' }}">
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at?->toIso8601String() }}">

    <!-- TWITTER / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    <meta name="twitter:description" content="{{ $twitterDescription }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">

    <!-- ORIGINAL FONTS AND STYLES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/css/post-details.css') }}">

    <!-- DYNAMIC NEWSARTICLE JSON-LD -->
    <script type="application/ld+json">
        {!! json_encode(
            $structuredData,
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

    <!-- SECTION 6: ORIGINAL TOP UTILITY BAR -->
    <div class="top-bar">
        <div id="live-clock"></div>

        <div class="top-bar-right">
            <span
                class="epaper-btn"
                data-bn="ই-পেপার"
                data-en="E-Paper"
            >ই-পেপার</span>

            <button
                id="lang-toggle"
                class="lang-btn"
                type="button"
                aria-label="Toggle Language"
            >English</button>
        </div>
    </div>


    <!-- SECTION 7: ORIGINAL HEADER -->
    <header>
        <a
            href="{{ url('/') }}"
            class="logo"
            aria-label="ডেইলি দরিয়ানগর হোমপেজ"
        >
            <img
                src="{{ asset('frontend/img/logo.png') }}"
                alt="ডেইলি দরিয়ানগর | Daily Dariyanagar"
                class="logo-img"
                fetchpriority="high"
            >
        </a>

        <form
            class="search-box"
            role="search"
            onsubmit="event.preventDefault();"
        >
            <input
                type="text"
                id="search-input"
                placeholder="খবর খুঁজুন..."
                data-placeholder-bn="খবর খুঁজুন..."
                data-placeholder-en="Search news..."
                aria-label="Search News"
            >

            <button
                type="submit"
                data-bn="খুঁজুন"
                data-en="Search"
            >খুঁজুন</button>
        </form>
    </header>


    <!-- SECTION 8: ORIGINAL STICKY NAVIGATION -->
    <nav id="nav-list" aria-label="Primary Navigation">

        <a
            href="{{ url('/') }}"
            data-bn="প্রচ্ছদ"
            data-en="Home"
        >প্রচ্ছদ</a>

        @foreach($categories as $category)
            <a
                href="{{ url('/') }}"
                data-bn="{{ $category->name_bn }}"
                data-en="{{ $category->name_en }}"
            >{{ $category->name_bn }}</a>
        @endforeach

    </nav>


    <!-- SECTION 9: ORIGINAL BREAKING NEWS TICKER -->
    <div class="ticker">

        <div class="ticker-title">
            <span class="live-dot"></span>

            <span
                data-bn="ব্রেকিং নিউজ"
                data-en="BREAKING NEWS"
            >ব্রেকিং নিউজ</span>
        </div>

        <marquee
            id="breaking-marquee"
            scrollamount="6"
            onmouseover="this.stop();"
            onmouseout="this.start();"
        >
            @foreach($breakingPosts as $breaking)
                <a
                    href="{{ route('news.show', ['slug' => $breaking->slug]) }}{{ $lang === 'en' ? '?lang=en' : '' }}"
                    data-bn="{{ $breaking->title_bn }}"
                    data-en="{{ $breaking->title_en }}"
                >{{ $breaking->title_bn }}</a>

                @unless($loop->last)
                    &nbsp; • &nbsp;
                @endunless
            @endforeach
        </marquee>

    </div>


    <!-- SECTION 10: ORIGINAL TWO-COLUMN ARTICLE LAYOUT -->
    <main class="main-container post-details-layout">

        <!-- LEFT COLUMN -->
        <div class="main-col-left">

            <!-- AD 1: TOP LEADERBOARD -->
            <div class="post-leaderboard-ad reveal active">
                <span
                    class="ad-label"
                    data-bn="বিজ্ঞাপন | টপ ব্যানার স্পন্সর"
                    data-en="Advertisement | Top Banner Sponsor"
                >বিজ্ঞাপন | টপ ব্যানার স্পন্সর</span>

                <div class="ad-slot ad-leaderboard-box">
                    <img
                        src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=900&h=140&fit=crop&q=80"
                        alt="Top Leaderboard Advertisement"
                        loading="lazy"
                        decoding="async"
                    >

                    <div
                        class="ad-overlay-text"
                        data-bn="টপ লিডারবোর্ড বিজ্ঞাপন | আপনার প্রতিষ্ঠানের প্রচারে যোগাযোগ করুন"
                        data-en="Top Leaderboard Ad | Contact Us to Promote Your Brand"
                    >টপ লিডারবোর্ড বিজ্ঞাপন | আপনার প্রতিষ্ঠানের প্রচারে যোগাযোগ করুন</div>
                </div>
            </div>


            <!-- MAIN ARTICLE -->
            <article class="article-section reveal active">

                <!-- BREADCRUMB -->
                <div class="breadcrumb">
                    <a
                        href="{{ url('/') }}"
                        data-bn="প্রচ্ছদ"
                        data-en="Home"
                    >প্রচ্ছদ</a>
                    /

                    <a href="{{ url('/') }}">
                        {{ $lang === 'bn'
                            ? ($post->category?->name_bn ?? 'সংবাদ')
                            : ($post->category?->name_en ?? 'News')
                        }}
                    </a>
                    /

                    <span
                        data-bn="বিস্তারিত প্রতিবেদন"
                        data-en="Full Report"
                    >বিস্তারিত প্রতিবেদন</span>
                </div>


                <!-- DYNAMIC ARTICLE HEADLINE -->
                <h1 class="article-title">{{ $headline }}</h1>


                <!-- ARTICLE METADATA -->
                <div class="article-meta">

                    <div class="author-info">
                        <i class="fa fa-user-circle"></i>
                        <span>{{ $reporter }}</span>
                    </div>

                    <div class="time-info">
                        <i class="fa fa-clock-o"></i>

                        <time datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $lang === 'bn' ? 'প্রকাশিত: ' : 'Published: ' }}
                            {{ $publishedLabel }}
                        </time>
                    </div>


                    <!-- ARTICLE SHARING -->
                    <div class="share-buttons" aria-label="Share Article">

                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($lang === 'en' ? $englishUrl : $articleUrl) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Share on Facebook"
                        >
                            <i
                                class="fa fa-facebook-square"
                                style="color:#3b5998;"
                                title="Share on Facebook"
                            ></i>
                        </a>

                        <a
                            href="https://twitter.com/intent/tweet?url={{ urlencode($lang === 'en' ? $englishUrl : $articleUrl) }}&text={{ urlencode($headline) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Share on Twitter"
                        >
                            <i
                                class="fa fa-twitter-square"
                                style="color:#1da1f2;"
                                title="Share on Twitter"
                            ></i>
                        </a>

                        <a
                            href="https://api.whatsapp.com/send?text={{ urlencode($headline . ' ' . ($lang === 'en' ? $englishUrl : $articleUrl)) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Share on WhatsApp"
                        >
                            <i
                                class="fa fa-whatsapp"
                                style="color:#25d366;"
                                title="Share on WhatsApp"
                            ></i>
                        </a>

                        <button
                            type="button"
                            class="print-article"
                            style="background:transparent;border:0;padding:0;color:inherit;font:inherit;cursor:pointer;"
                            onclick="window.print();"
                            aria-label="Print Article"
                        >
                            <i
                                class="fa fa-print"
                                style="color:#555;"
                                title="Print Article"
                            ></i>
                        </button>

                    </div>
                </div>


                <!-- DYNAMIC FEATURED IMAGE -->
                @if($featuredImageUrl)
                    <div class="article-featured-image">

                        <img
                            src="{{ $featuredImageUrl }}"
                            alt="{{ $lang === 'bn'
                                ? ($post->image_alt_bn ?: $headline)
                                : ($post->image_alt_en ?: $headline)
                            }}"
                            fetchpriority="high"
                        >

                        @if(filled($imageCaption))
                            <div class="image-caption">
                                {{ $imageCaption }}
                            </div>
                        @endif

                    </div>
                @endif


                <!-- FULL ARTICLE BODY -->
                <div class="article-body">

                    {!! $contentParts[0] !!}


                    <!-- AD 2: IN-ARTICLE UPPER AD -->
                    <div class="middle-ad">
                        <span
                            class="ad-label"
                            data-bn="বিজ্ঞাপন | স্পন্সরড কন্টেন্ট"
                            data-en="Advertisement | Sponsored Content"
                        >বিজ্ঞাপন | স্পন্সরড কন্টেন্ট</span>

                        <div class="ad-slot ad-inpost-banner">
                            <img
                                src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=160&fit=crop&q=80"
                                alt="In-Article Upper Advertisement"
                                loading="lazy"
                                decoding="async"
                            >

                            <div
                                class="ad-overlay-text"
                                data-bn="স্পন্সরড ব্যানার | ব্যবসার ডিজিটাল প্রসারে বিজ্ঞাপন দিন"
                                data-en="Sponsored Banner | Advertise for Digital Business Growth"
                            >স্পন্সরড ব্যানার | ব্যবসার ডিজিটাল প্রসারে বিজ্ঞাপন দিন</div>
                        </div>
                    </div>


                    {!! $contentParts[1] !!}


                    <!-- AD 3: IN-ARTICLE MIDDLE AD -->
                    <div class="middle-ad">
                        <span
                            class="ad-label"
                            data-bn="বিজ্ঞাপন | স্পন্সরড"
                            data-en="Advertisement | Sponsored"
                        >বিজ্ঞাপন | স্পন্সরড</span>

                        <img
                            src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800&h=180&fit=crop&q=80"
                            alt="In-Article Middle Advertisement"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>


                    {!! $contentParts[2] !!}


                    <!-- AD 4: DUAL PROMOTIONAL ADS -->
                    <div class="in-post-dual-ads">

                        <div class="ad-slot ad-dual-card">
                            <img
                                src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=450&q=80"
                                alt="Real Estate Sponsor Ad"
                                loading="lazy"
                                decoding="async"
                            >

                            <div
                                class="ad-overlay-text"
                                data-bn="বিজ্ঞাপন | চট্টগ্রামে প্রিমিয়াম অ্যাপার্টমেন্ট বুকিং চলছে"
                                data-en="Sponsored | Premium Apartment Booking Open in Chattogram"
                            >বিজ্ঞাপন | চট্টগ্রামে প্রিমিয়াম অ্যাপার্টমেন্ট বুকিং চলছে</div>
                        </div>

                        <div class="ad-slot ad-dual-card">
                            <img
                                src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=450&q=80"
                                alt="Logistics Sponsor Ad"
                                loading="lazy"
                                decoding="async"
                            >

                            <div
                                class="ad-overlay-text"
                                data-bn="বিজ্ঞাপন | গ্লোবাল শিপিং ও লজিস্টিকস পার্টনার"
                                data-en="Sponsored | Global Shipping & Logistics Partner"
                            >বিজ্ঞাপন | গ্লোবাল শিপিং ও লজিস্টিকস পার্টনার</div>
                        </div>

                    </div>
                </div>


                <!-- ARTICLE TOPICS -->
                <div class="article-tags">

                    <strong
                        data-bn="সম্পর্কিত বিষয়:"
                        data-en="Related Topics:"
                    >সম্পর্কিত বিষয়:</strong>

                    @if($post->category)
                        <a href="{{ url('/') }}">
                            {{ $lang === 'bn'
                                ? $post->category->name_bn
                                : $post->category->name_en
                            }}
                        </a>
                    @endif

                    @if(filled($focusKeyword))
                        <a href="{{ url('/') }}">
                            {{ $focusKeyword }}
                        </a>
                    @endif

                </div>
            </article>


            <!-- AD 5: POST-BOTTOM FULL BANNER -->
            <div class="post-leaderboard-ad reveal">

                <span
                    class="ad-label"
                    data-bn="বিজ্ঞাপন | বটম স্পন্সর ব্যানার"
                    data-en="Advertisement | Bottom Sponsor Banner"
                >বিজ্ঞাপন | বটম স্পন্সর ব্যানার</span>

                <div class="ad-slot ad-leaderboard-box">
                    <img
                        src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=900&h=150&fit=crop&q=80"
                        alt="Post Bottom Advertisement"
                        loading="lazy"
                        decoding="async"
                    >

                    <div
                        class="ad-overlay-text"
                        data-bn="ডেইলি দরিয়ানগরে বিজ্ঞাপন দিয়ে আপনার ব্র্যান্ডকে পৌঁছে দিন লক্ষ পাঠকের কাছে"
                        data-en="Reach Hundreds of Thousands of Readers by Advertising With Daily Dariyanagar"
                    >ডেইলি দরিয়ানগরে বিজ্ঞাপন দিয়ে আপনার ব্র্যান্ডকে পৌঁছে দিন লক্ষ পাঠকের কাছে</div>
                </div>
            </div>


            <!-- RELATED NEWS -->
            <section class="related-posts-section reveal">

                <div
                    class="section-title"
                    data-bn="সম্পর্কিত আরও সংবাদ"
                    data-en="Related News"
                >সম্পর্কিত আরও সংবাদ</div>

                <div class="grid-3-col" id="related-posts-grid">

                    @forelse($relatedPosts as $related)

                        <a
                            class="sub-card"
                            href="{{ route('news.show', ['slug' => $related->slug]) }}{{ $lang === 'en' ? '?lang=en' : '' }}"
                        >

                            @if($related->featured_image)
                                <img
                                    src="{{ $publicImage($related->featured_image) }}"
                                    alt="{{ $lang === 'bn'
                                        ? ($related->image_alt_bn ?: $related->title_bn)
                                        : ($related->image_alt_en ?: $related->title_en)
                                    }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @else
                                <img
                                    src="{{ asset('frontend/img/logo.png') }}"
                                    alt="Daily Dariyanagar"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @endif

                            <div class="sub-card-body">
                                <h3>
                                    {{ $lang === 'bn'
                                        ? $related->title_bn
                                        : $related->title_en
                                    }}
                                </h3>
                            </div>

                        </a>

                    @empty

                        <p>
                            {{ $lang === 'bn'
                                ? 'সম্পর্কিত কোনো সংবাদ নেই।'
                                : 'No related articles yet.'
                            }}
                        </p>

                    @endforelse
                </div>
            </section>

        </div>
        <!-- END LEFT COLUMN -->


        <!-- ORIGINAL RIGHT SIDEBAR -->
        <aside class="main-col-middle">

            <!-- RIGHT BAR AD 1 -->
            <div class="ad-slot ad-banner-box reveal active">
                <img
                    src="https://images.unsplash.com/photo-1557804506-669a67965ba0?w=500&q=80"
                    alt="Top Sidebar Advertisement"
                    loading="lazy"
                    decoding="async"
                >

                <div
                    class="ad-overlay-text"
                    data-bn="বিজ্ঞাপন এলাকা | টপ সাইডবার স্পন্সর"
                    data-en="Advertisement Area | Top Sidebar Sponsor"
                >বিজ্ঞাপন এলাকা | টপ সাইডবার স্পন্সর</div>
            </div>


            <!-- LATEST NEWS SIDEBAR -->
            <div class="sidebar reveal active" id="side-feed">

                <h3
                    class="sidebar-title"
                    data-bn="সর্বশেষ আপডেট"
                    data-en="Latest Updates"
                >সর্বশেষ আপডেট</h3>

                <div id="side-feed-list">

                    @forelse($latestPosts as $latest)

                        <a
                            class="feed-item"
                            href="{{ route('news.show', ['slug' => $latest->slug]) }}{{ $lang === 'en' ? '?lang=en' : '' }}"
                        >

                            <img
                                src="{{ $latest->featured_image
                                    ? $publicImage($latest->featured_image)
                                    : asset('frontend/img/logo.png')
                                }}"
                                alt="{{ $lang === 'bn'
                                    ? ($latest->image_alt_bn ?: $latest->title_bn)
                                    : ($latest->image_alt_en ?: $latest->title_en)
                                }}"
                                loading="lazy"
                                decoding="async"
                            >

                            <div>
                                <h4>
                                    {{ $lang === 'bn'
                                        ? $latest->title_bn
                                        : $latest->title_en
                                    }}
                                </h4>

                                <time
                                    class="feed-time"
                                    datetime="{{ $latest->published_at?->toIso8601String() }}"
                                >
                                    <i class="fa fa-clock-o"></i>
                                    {{ $latest->published_at?->locale($lang)->diffForHumans() }}
                                </time>
                            </div>

                        </a>

                    @empty

                        <p>
                            {{ $lang === 'bn'
                                ? 'এখনো কোনো প্রকাশিত সংবাদ নেই।'
                                : 'No published news yet.'
                            }}
                        </p>

                    @endforelse
                </div>
            </div>


            <!-- RIGHT BAR AD 2 -->
            <div class="ad-slot ad-promo-box reveal">
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
                >প্রোমোশনাল ব্যানার | ব্যবসার প্রসারে বিজ্ঞাপন দিন</div>
            </div>


            <!-- MOST READ NEWS -->
            <div class="sidebar reveal" id="most-read-box">

                <h3
                    class="sidebar-title"
                    style="color: var(--primary-red);"
                    data-bn="সর্বাধিক পঠিত"
                    data-en="Most Read News"
                >সর্বাধিক পঠিত</h3>

                <div id="most-read-list">

                    @forelse($mostReadPosts as $popular)

                        <a
                            class="most-read-item"
                            href="{{ route('news.show', ['slug' => $popular->slug]) }}{{ $lang === 'en' ? '?lang=en' : '' }}"
                        >

                            <span class="most-read-rank">
                                {{ $lang === 'bn'
                                    ? strtr(
                                        (string) $loop->iteration,
                                        [
                                            '0'=>'০',
                                            '1'=>'১',
                                            '2'=>'২',
                                            '3'=>'৩',
                                            '4'=>'৪',
                                            '5'=>'৫',
                                            '6'=>'৬',
                                            '7'=>'৭',
                                            '8'=>'৮',
                                            '9'=>'৯'
                                        ]
                                    )
                                    : $loop->iteration
                                }}
                            </span>

                            <h4>
                                {{ $lang === 'bn'
                                    ? $popular->title_bn
                                    : $popular->title_en
                                }}
                            </h4>

                        </a>

                    @empty

                        <p>
                            {{ $lang === 'bn'
                                ? 'কোনো সংবাদ নেই।'
                                : 'No articles yet.'
                            }}
                        </p>

                    @endforelse
                </div>
            </div>


            <!-- RIGHT BAR AD 3 -->
            <div class="ad-slot ad-banner-box reveal">
                <img
                    src="https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&q=80"
                    alt="Financial Sponsor Advertisement"
                    loading="lazy"
                    decoding="async"
                >

                <div
                    class="ad-overlay-text"
                    data-bn="বিজ্ঞাপন এলাকা | ব্যাংকিং ও ফিন্যান্স পার্টনার"
                    data-en="Advertisement Area | Banking & Finance Partner"
                >বিজ্ঞাপন এলাকা | ব্যাংকিং ও ফিন্যান্স পার্টনার</div>
            </div>


            <!-- ORIGINAL E-PAPER CARD -->
            <div class="right-info-card reveal">

                <div
                    class="right-info-header"
                    data-bn="আজকের ই-পেপার"
                    data-en="Today's Print E-Paper"
                >আজকের ই-পেপার</div>

                <div class="right-info-body">

                    <img
                        src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=400&q=80"
                        alt="আজকের ই-পেপার"
                        loading="lazy"
                        decoding="async"
                    >

                    <p
                        data-bn="ছাপার কাগজের হুবহু সংস্করণ পড়ুন আপনার মোবাইল ও কম্পিউটারে।"
                        data-en="Read the exact replica of today's print edition on your device."
                    >ছাপার কাগজের হুবহু সংস্করণ পড়ুন আপনার মোবাইল ও কম্পিউটারে।</p>

                    <a
                        href="#"
                        class="epaper-card-btn"
                        data-bn="ই-পেপার পড়ুন"
                        data-en="Read E-Paper Now"
                    >ই-পেপার পড়ুন</a>

                </div>
            </div>


            <!-- RIGHT BAR AD 4 -->
            <div class="ad-slot ad-skyscraper-box reveal">
                <img
                    src="https://images.unsplash.com/photo-1516549655169-df83a0774514?w=500&q=80"
                    alt="Healthcare Sponsor Banner"
                    loading="lazy"
                    decoding="async"
                >

                <div
                    class="ad-overlay-text"
                    data-bn="বিশেষ বিজ্ঞাপন | আধুনিক স্বাস্থ্যসেবা ও ডায়াগনস্টিক সেন্টার"
                    data-en="Special Sponsor | Modern Healthcare & Diagnostic Center"
                >বিশেষ বিজ্ঞাপন | আধুনিক স্বাস্থ্যসেবা ও ডায়াগনস্টিক সেন্টার</div>
            </div>

        </aside>

    </main>


    <!-- SECTION 11: ORIGINAL FOOTER -->
    <footer class="main-footer">

        <div class="footer-top">

            <div class="footer-logo">
                <a
                    href="{{ url('/') }}"
                    aria-label="ডেইলি দরিয়ানগর ফুটার লোগো"
                >
                    <img
                        src="{{ asset('frontend/img/logo.png') }}"
                        alt="ডেইলি দরিয়ানগর | Daily Dariyanagar"
                        class="footer-logo-img"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
            </div>

            <div class="footer-socials">
                <a href="#" aria-label="Facebook">
                    <i class="fa fa-facebook"></i>
                </a>

                <a href="#" aria-label="Twitter">
                    <i class="fa fa-twitter"></i>
                </a>

                <a href="#" aria-label="YouTube">
                    <i class="fa fa-youtube"></i>
                </a>

                <a href="#" aria-label="Instagram">
                    <i class="fa fa-instagram"></i>
                </a>
            </div>

        </div>


        <div class="footer-links-grid">

            <div>
                <a href="{{ url('/') }}" data-bn="জাতীয়" data-en="National">জাতীয়</a>
                <a href="{{ url('/') }}" data-bn="চট্টগ্রাম" data-en="Chattogram">চট্টগ্রাম</a>
                <a href="{{ url('/') }}" data-bn="দেশজুড়ে" data-en="Countrywide">দেশজুড়ে</a>
                <a href="{{ url('/') }}" data-bn="রাজনীতি" data-en="Politics">রাজনীতি</a>
            </div>

            <div>
                <a href="{{ url('/') }}" data-bn="আন্তর্জাতিক" data-en="International">আন্তর্জাতিক</a>
                <a href="{{ url('/') }}" data-bn="খেলাধুলা" data-en="Sports">খেলাধুলা</a>
                <a href="{{ url('/') }}" data-bn="অর্থ-বাণিজ্য" data-en="Economy">অর্থ-বাণিজ্য</a>
                <a href="{{ url('/') }}" data-bn="আইন-কানুন" data-en="Law & Justice">আইন-কানুন</a>
            </div>

            <div>
                <a href="{{ url('/') }}" data-bn="আলোচিত সংবাদ" data-en="Trending News">আলোচিত সংবাদ</a>
                <a href="{{ url('/') }}" data-bn="অনুসন্ধান" data-en="Investigation">অনুসন্ধান</a>
                <a href="{{ url('/') }}" data-bn="এক্সক্লুসিভ" data-en="Exclusive">এক্সক্লুসিভ</a>
                <a href="{{ url('/') }}" data-bn="দুর্নীতি ও অপরাধ" data-en="Crime & Anti-Corruption">দুর্নীতি ও অপরাধ</a>
            </div>

            <div>
                <a href="{{ url('/') }}" data-bn="জার্নাল" data-en="Journal">জার্নাল</a>
                <a href="{{ url('/') }}" data-bn="এইসময়" data-en="Contemporary">এইসময়</a>
                <a href="{{ url('/') }}" data-bn="তথ্যপ্রযুক্তি" data-en="Tech & AI">তথ্যপ্রযুক্তি</a>
                <a href="{{ url('/') }}" data-bn="পরিবেশ-প্রতিবেশ" data-en="Environment">পরিবেশ-প্রতিবেশ</a>
            </div>

            <div>
                <a href="{{ url('/') }}" data-bn="ধর্ম ও জীবন" data-en="Religion & Life">ধর্ম ও জীবন</a>
                <a href="{{ url('/') }}" data-bn="লাইফস্টাইল" data-en="Lifestyle">লাইফস্টাইল</a>
                <a href="{{ url('/') }}" data-bn="শিক্ষাঙ্গন" data-en="Education">শিক্ষাঙ্গন</a>
                <a href="{{ url('/') }}" data-bn="শিল্প ও সাহিত্য" data-en="Art & Literature">শিল্প ও সাহিত্য</a>
            </div>

            <div>
                <a href="{{ url('/') }}" data-bn="স্বাস্থ্য" data-en="Health">স্বাস্থ্য</a>
                <a href="{{ url('/') }}" data-bn="বিনোদন" data-en="Entertainment">বিনোদন</a>
                <a href="{{ url('/') }}" data-bn="ভ্রমণ" data-en="Travel">ভ্রমণ</a>
                <a href="{{ url('/') }}" data-bn="মুক্তচিন্তা" data-en="Opinion">মুক্তচিন্তা</a>
            </div>

        </div>


        <div class="footer-info" id="footer-editorial-info">

            <span
                data-bn="© ডেইলি দরিয়ানগর ২০১৫ - ২০২৬ । তথ্য অধিদফতরের নিবন্ধন নম্বর : ৬৪"
                data-en="© Daily Dariyanagar 2015 - 2026 | PID Registration No: 64"
            >© ডেইলি দরিয়ানগর ২০১৫ - ২০২৬ । তথ্য অধিদফতরের নিবন্ধন নম্বর : ৬৪</span>
            <br>

            <span
                data-bn="প্রতিষ্ঠাতা সম্পাদক : আহমদ উল্লাহ"
                data-en="Founding Editor: Ahmad Ullah"
            >প্রতিষ্ঠাতা সম্পাদক : আহমদ উল্লাহ</span>
            <br>

            <span
                data-bn="ভারপ্রাপ্ত সম্পাদক : মোহাম্মদ রফিকুল ইসলাম"
                data-en="Acting Editor: Mohammad Rafiqul Islam"
            >ভারপ্রাপ্ত সম্পাদক : মোহাম্মদ রফিকুল ইসলাম</span>
            <br>

            <span
                data-bn="ভবন ১২, রোড ৫ | ৪৫ পোর্ট এভিনিউ, চট্টগ্রাম-৪০০০ |"
                data-en="Building 12, Road 5 | 45 Port Avenue, Chattogram-4000 |"
            >ভবন ১২, রোড ৫ | ৪৫ পোর্ট এভিনিউ, চট্টগ্রাম-৪০০০ |</span>
            <br>

            <span
                data-bn="ইমেইল : news@dailydariyanagar.com (নিউজ), info@dailydariyanagar.com (সম্পাদক) | ফোন : ০২৩৩৩৩৭৫৮৪৪"
                data-en="Email: news@dailydariyanagar.com (News), info@dailydariyanagar.com (Editor) | Phone: +880-2333375844"
            >ইমেইল : news@dailydariyanagar.com (নিউজ), info@dailydariyanagar.com (সম্পাদক) | ফোন : ০২৩৩৩৩৭৫৮৪৪</span>

        </div>


        <div class="footer-bottom-bar">

            <span
                data-bn="এই ওয়েবসাইটের কোনো লেখা বা লেখার অংশবিশেষ কিংবা ছবি বিনা অনুমতিতে অন্য কোথাও প্রচার বা প্রকাশ করা নিষিদ্ধ | ওয়েবসাইটটি তৈরি করেছেন:"
                data-en="Reproduction of any content or image from this website without prior permission is strictly prohibited | Website Developed by:"
            >এই ওয়েবসাইটের কোনো লেখা বা লেখার অংশবিশেষ কিংবা ছবি বিনা অনুমতিতে অন্য কোথাও প্রচার বা প্রকাশ করা নিষিদ্ধ | ওয়েবসাইটটি তৈরি করেছেন:</span>

            <a
                href="https://wahedbulbulorpon.com"
                target="_blank"
                rel="noopener noreferrer"
                style="color: #ffeb3b; text-decoration: underline; font-weight: bold;"
            >Wahed Bul Bul Orpon</a>

        </div>

    </footer>


    <!-- ORIGINAL SCROLL TO TOP -->
    <div
        class="scroll-top"
        id="scrollTopBtn"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'});"
        aria-label="Scroll to top"
    >
        <i class="fa fa-chevron-up"></i>
    </div>


    <!-- DYNAMIC PAGE JAVASCRIPT -->
    <script
        src="{{ asset('frontend/js/post-details.js') }}"
        defer
    ></script>

</body>
</html>