
<!DOCTYPE html>
<html lang="{{ $lang }}">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="theme-color"
        content="#000000"
    >

    <title>
        {{ $lang === 'bn' ? 'ই-পেপার' : 'E-Paper' }}
        | Daily Dariyanagar
    </title>

    <meta
        name="description"
        content="{{ $lang === 'bn'
            ? 'ডেইলি দরিয়ানগর ই-পেপার। দৈনিক সংবাদপত্র পড়ুন এবং পিডিএফ ডাউনলোড করুন।'
            : 'Read Daily Dariyanagar E-Paper online and download newspaper PDF editions.'
        }}"
    >

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
        href="https://fonts.googleapis.com/css2?family=Noto+Serif+Bengali:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="{{ asset('frontend/css/epaper.css') }}"
    >

</head>

<body class="epaper-body">

@php

    /*
    |--------------------------------------------------------------------------
    | Bengali page numbers
    |--------------------------------------------------------------------------
    */

    $bnDigits = function ($value) {

        return strtr((string) $value, [
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


    /*
    |--------------------------------------------------------------------------
    | Newspaper page labels
    |--------------------------------------------------------------------------
    */

    $pageLabel = function (int $page) use ($lang, $bnDigits) {

        if ($lang === 'en') {

            if ($page === 1) return '1st Page';
            if ($page === 2) return '2nd Page';
            if ($page === 3) return '3rd Page';

            return $page . 'th Page';

        }

        if ($page === 1) return 'প্রথম পাতা';
        if ($page === 2) return '২য় পাতা';
        if ($page === 3) return '৩য় পাতা';

        return $bnDigits($page) . ' পাতা';

    };


    /*
    |--------------------------------------------------------------------------
    | Page and language parameters
    |--------------------------------------------------------------------------
    */

    $languageParams = $lang === 'en'
        ? ['lang' => 'en']
        : [];

    $editionParams = array_merge(
        [
            'date' => $edition['date'],
        ],
        $languageParams
    );


    /*
    |--------------------------------------------------------------------------
    | Main website URL
    |--------------------------------------------------------------------------
    */

    $homeUrl = route(
        'home',
        $languageParams
    );


    /*
    |--------------------------------------------------------------------------
    | Complete edition download
    |--------------------------------------------------------------------------
    */

    $downloadFullUrl = route(
        'epaper.download',
        $editionParams
    );


    /*
    |--------------------------------------------------------------------------
    | FIX: Prepare JavaScript data as a separate PHP variable
    |
    | Never pass this complex array directly to @json().
    |--------------------------------------------------------------------------
    */

    $readerData = [
        'date' => $edition['date'],
        'lang' => $lang,
        'page' => $pageNumber,
        'count' => count($edition['pages']),
        'archive' => array_values($edition['archive']),
        'baseUrl' => route('epaper.index'),
    ];

@endphp


<!-- ==================================================
     MAIN EPAPER WRAPPER
================================================== -->

<div class="epaper-shell">


    <!-- ==============================================
         HEADER — RED BENGALI NEWSPAPER BRANDING
    =============================================== -->

    <header class="epaper-top-header">

        <div class="epaper-logo-wrap">

            <a
                href="{{ $homeUrl }}"
                class="epaper-brand"
            >

                {{ config('epaper.name_bn', 'ডেইলি দরিয়ানগর') }}

            </a>

        </div>

    </header>


    <!-- ==============================================
         BLACK NAVIGATION TOOLBAR
    =============================================== -->

    <nav class="epaper-toolbar">

        <div class="epaper-toolbar-left">

            <!-- HOME -->

            <a
                href="{{ $homeUrl }}"
                class="toolbar-home"
                title="Home"
                aria-label="Go to homepage"
            >
                &#8962;
            </a>


            <!-- DOWNLOAD COMPLETE EDITION -->

            @if(count($edition['pages']) > 0)

                <a
                    href="{{ $downloadFullUrl }}"
                    class="toolbar-download"
                >

                    &#8595;

                    {{ $lang === 'bn'
                        ? 'ডাউনলোড'
                        : 'Download'
                    }}

                </a>

            @endif


            <!-- DYNAMIC NEWSPAPER PAGE TABS -->

            @foreach($edition['pages'] as $page)

                <button
                    type="button"
                    class="toolbar-page-tab {{ $page['number'] === $pageNumber ? 'is-active' : '' }}"
                    data-go-page="{{ $page['number'] }}"
                >

                    {{ $pageLabel($page['number']) }}

                </button>

            @endforeach

        </div>


        <!-- RIGHT TOOLBAR ACTIONS -->

        <div class="epaper-toolbar-right">

            <a
                href="{{ $homeUrl }}"
                class="toolbar-link"
            >

                {{ $lang === 'bn'
                    ? 'অনলাইন ভার্সন'
                    : 'Online Version'
                }}

            </a>


            <button
                type="button"
                class="toolbar-print"
                onclick="window.print()"
            >

                &#128424;

                {{ $lang === 'bn'
                    ? 'প্রিন্ট'
                    : 'Print'
                }}

            </button>


            <a
                href="{{ route('epaper.index', array_merge(
                    ['date' => $edition['date']],
                    ['lang' => $lang === 'bn' ? 'en' : 'bn']
                )) }}"
                class="toolbar-link"
            >

                {{ $lang === 'bn'
                    ? 'English'
                    : 'বাংলা'
                }}

            </a>

        </div>

    </nav>


    <!-- ==============================================
         MAIN THREE-COLUMN EPAPER LAYOUT
    =============================================== -->

    <main class="epaper-main-layout">


        <!-- ==========================================
             LEFT SIDEBAR — ALL NEWSPAPER PAGES
        =========================================== -->

        <aside class="epaper-left-sidebar">

            <div class="sidebar-title">

                {{ $lang === 'bn'
                    ? 'সকল পাতা'
                    : 'All Pages'
                }}

            </div>


            <div class="left-pages-scroll">

                @foreach($edition['pages'] as $page)

                    <div
                        class="thumb-card {{ $page['number'] === $pageNumber ? 'is-active' : '' }}"
                        data-go-page="{{ $page['number'] }}"
                        role="button"
                        tabindex="0"
                        aria-label="{{ $pageLabel($page['number']) }}"
                    >

                        <div class="thumb-paper-frame">

                            <div class="thumb-paper-scale">

                                @include('epaper.sheet', [
                                    'page' => $page,
                                    'forPdf' => false,
                                ])

                            </div>

                        </div>


                        <span class="thumb-page-label">

                            {{ $pageLabel($page['number']) }}

                        </span>

                    </div>

                @endforeach

            </div>

        </aside>


        <!-- ==========================================
             CENTER — MAIN NEWSPAPER PAGE PREVIEW
        =========================================== -->

        <section class="epaper-center-preview">

            <div class="preview-stage">

                @forelse($edition['pages'] as $page)

                    <div
                        class="preview-page-slot {{ $page['number'] === $pageNumber ? 'is-active' : '' }}"
                        data-page="{{ $page['number'] }}"
                    >

                        <div class="preview-paper-shell">

                            @include('epaper.sheet', [
                                'page' => $page,
                                'forPdf' => false,
                            ])

                        </div>

                    </div>

                @empty

                    <div class="epaper-empty">

                        <h2>

                            {{ $lang === 'bn'
                                ? 'কোনো সংস্করণ পাওয়া যায়নি'
                                : 'No Edition Found'
                            }}

                        </h2>


                        <p>

                            {{ $lang === 'bn'
                                ? 'এই তারিখে প্রকাশিত কোনো পত্রিকা নেই।'
                                : 'No published newspaper is available for this date.'
                            }}

                        </p>

                    </div>

                @endforelse

            </div>

        </section>


        <!-- ==========================================
             RIGHT SIDEBAR
        =========================================== -->

        <aside class="epaper-right-sidebar">


            <!-- ======================================
                 OLD EDITIONS CALENDAR
            ======================================= -->

            <div class="sidebar-title">

                {{ $lang === 'bn'
                    ? 'পুরানো সংখ্যা'
                    : 'Old Editions'
                }}

            </div>


            <div class="archive-box">

                <div class="calendar-header">

                    <button
                        type="button"
                        id="calendar-prev-month"
                        class="calendar-nav"
                        aria-label="Previous month"
                    >
                        &#9664;
                    </button>


                    <strong id="calendar-month-label"></strong>


                    <button
                        type="button"
                        id="calendar-next-month"
                        class="calendar-nav"
                        aria-label="Next month"
                    >
                        &#9654;
                    </button>

                </div>


                <div class="calendar-weekdays">

                    <span>Su</span>
                    <span>Mo</span>
                    <span>Tu</span>
                    <span>We</span>
                    <span>Th</span>
                    <span>Fr</span>
                    <span>Sa</span>

                </div>


                <div
                    id="calendar-grid"
                    class="calendar-grid"
                ></div>

            </div>


            <!-- ======================================
                 CURRENT EDITION PAGE LIST
            ======================================= -->

            <div class="sidebar-title sidebar-gap">

                {{ $lang === 'bn'
                    ? 'আজকের পত্রিকা'
                    : 'Current Edition'
                }}

            </div>


            <div class="today-pages-box">

                @foreach($edition['pages'] as $page)

                    <button
                        type="button"
                        class="today-page-item {{ $page['number'] === $pageNumber ? 'is-active' : '' }}"
                        data-go-page="{{ $page['number'] }}"
                    >

                        <span class="today-check">
                            &#10004;
                        </span>


                        <span>
                            {{ $pageLabel($page['number']) }}
                        </span>

                    </button>

                @endforeach

            </div>


            <!-- ======================================
                 DOWNLOAD CURRENT EDITION
            ======================================= -->

            @if(count($edition['pages']) > 0)

                <div class="sidebar-title sidebar-gap">

                    {{ $lang === 'bn'
                        ? 'পিডিএফ ডাউনলোড'
                        : 'PDF Downloads'
                    }}

                </div>


                <div class="today-pages-box">

                    <a
                        class="today-page-item"
                        href="{{ $downloadFullUrl }}"
                    >

                        <span class="today-check">
                            &#8595;
                        </span>


                        <span>

                            {{ $lang === 'bn'
                                ? 'সম্পূর্ণ পত্রিকা ডাউনলোড'
                                : 'Download Full Edition'
                            }}

                        </span>

                    </a>


                    @foreach($edition['pages'] as $page)

                        <a
                            class="today-page-item"
                            href="{{ route('epaper.download.page', array_merge(
                                ['page' => $page['number']],
                                $editionParams
                            )) }}"
                        >

                            <span class="today-check">
                                &#8595;
                            </span>


                            <span>

                                {{ $pageLabel($page['number']) }}

                                PDF

                            </span>

                        </a>

                    @endforeach

                </div>

            @endif


            <!-- ======================================
                 FACEBOOK BOX
            ======================================= -->

            <div class="sidebar-title sidebar-gap">

                {{ $lang === 'bn'
                    ? 'ফেসবুকে আমরা'
                    : 'Find Us On Facebook'
                }}

            </div>


            <div class="facebook-box">

                <div class="facebook-box-inner">

                    <div class="fb-brand">

                        {{ config('epaper.name_bn', 'ডেইলি দরিয়ানগর') }}

                    </div>


                    <div class="fb-sub">
                        Facebook Page
                    </div>

                </div>

            </div>


        </aside>

    </main>

</div>


<!-- ==================================================
     SAFE JAVASCRIPT DATA — PARSE ERROR FIXED
================================================== -->

<script>

    window.EPAPER_DATA = {{ \Illuminate\Support\Js::from($readerData) }};

</script>


<!-- EXISTING EPAPER JAVASCRIPT -->

<script
    src="{{ asset('frontend/js/epaper.js') }}"
    defer
></script>

</body>
</html>
