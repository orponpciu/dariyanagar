
<!DOCTYPE html>
<html lang="{{ $lang }}">

<head>

<meta charset="UTF-8">

<style>

@font-face {
    font-family: 'DariyaPrint';

    src: url(
        'data:font/ttf;base64,{{ $fonts['regular'] }}'
    ) format('truetype');

    font-weight: 400;
}

@font-face {
    font-family: 'DariyaPrint';

    src: url(
        'data:font/ttf;base64,{{ $fonts['bold'] }}'
    ) format('truetype');

    font-weight: 700;
}

{!! $newspaperCss !!}

@page {
    size: A4 portrait;
    margin: 0;
}

html,
body {
    margin: 0;
    padding: 0;
    background: #fff;
}

.ep-column-print {
    box-sizing: border-box;

    background: #fdf7e9;

    width: 210mm;
    min-height: 296mm;

    padding: 13mm 14mm 12mm;

    font-family: 'DariyaPrint', serif;

    color: #241d17;
}

.ep-column-print .ep-masthead-name {
    font-size: 27pt;
}

.ep-column-tag {
    text-align: center;

    background: #842c22;
    color: white;

    padding: 7px 9px;

    margin: 10px 0 14px;

    font-weight: 700;
}

.ep-column-story {
    border-bottom: 1px solid #988a75;

    padding: 10px 0 13px;

    break-inside: avoid;
    page-break-inside: avoid;
}

.ep-column-story h2 {
    font-size: 19pt;
    line-height: 1.5;

    margin: 5px 0 9px;
}

.ep-column-story p {
    font-size: 10.4pt;
    line-height: 1.7;

    text-align: justify;
}

.ep-column-photo {
    max-width: 100%;
    max-height: 80mm;

    object-fit: cover;

    display: block;

    margin: 8px 0;
}

</style>

</head>

<body>

<section class="ep-column-print">

    <!-- NEWSPAPER MASTHEAD -->

    <header class="ep-masthead">

        <div class="ep-masthead-ornament">
            ✦ DAILY DARIYANAGAR ✦
        </div>


        <div class="ep-masthead-name">

            {{ config('epaper.name_' . $lang) }}

        </div>


        <div class="ep-masthead-sub">

            {{ config('epaper.strapline_' . $lang) }}

        </div>

    </header>


    <!-- EDITION INFORMATION -->

    <div class="ep-issue-meta">

        <span>
            {{ $edition['date'] }}
        </span>

        <span>

            {{ $lang === 'bn'
                ? 'পৃষ্ঠা'
                : 'PAGE'
            }}

            {{ $page['number'] }}

        </span>

        <span>

            {{ $lang === 'bn'
                ? 'কলাম'
                : 'COLUMN'
            }}

            {{ $columnNumber }}

        </span>

    </div>


    <!-- COLUMN LABEL -->

    <div class="ep-column-tag">

        {{ $lang === 'bn'
            ? 'সংবাদপত্রের পৃথক কলাম'
            : 'NEWSPAPER COLUMN'
        }}

    </div>


    <!-- POSTS FROM SELECTED COLUMN -->

    @foreach($stories as $news)

        <section class="ep-column-story">

            <div class="ep-story-category">

                {{ $epaper->category(
                    $news,
                    $lang
                ) }}

            </div>


            <h2>

                <a href="{{ $epaper->storyUrl($news, $lang) }}">

                    {{ $epaper->title(
                        $news,
                        $lang
                    ) }}

                </a>

            </h2>


            @if(!empty($images[$news->id]))

                <img
                    class="ep-column-photo"
                    src="{{ $images[$news->id] }}"
                    alt="{{ $epaper->title($news, $lang) }}"
                >

            @endif


            <p>

                {{ $epaper->summary(
                    $news,
                    $lang,
                    $page['kind'] === 'cover'
                        && $columnNumber === 1
                        ? 700
                        : 240
                ) }}

            </p>


            <a
                class="ep-read-link"
                href="{{ $epaper->storyUrl($news, $lang) }}"
            >

                {{ $lang === 'bn'
                    ? 'বিস্তারিত প্রতিবেদন'
                    : 'FULL ARTICLE'
                }}

                ↗

            </a>

        </section>

    @endforeach


    <!-- NEWSPAPER FOOTER -->

    <footer class="ep-paper-footer">

        <span>
            DAILY DARIYANAGAR
        </span>

        <span>
            {{ $edition['date'] }}
        </span>

        <span>

            {{ $page['number'] }}
            /
            {{ $columnNumber }}

        </span>

    </footer>

</section>

</body>
</html>
