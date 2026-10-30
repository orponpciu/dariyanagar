<!DOCTYPE html>
<html lang="{{ $lang }}">

<head>
<meta charset="UTF-8">

<style>

body {
    font-family: dariyaserif;
    color: #2b241b;
    font-size: 10pt;
}

a {
    color: #2b241b;
    text-decoration: none;
}

.column-border {
    border-top: 3px double #2b241b;
    padding-top: 7px;
}

.masthead {
    text-align: center;
    border-bottom: 3px double #2b241b;
    margin-bottom: 7px;
    padding-bottom: 5px;
}

.brand {
    font-size: 25pt;
    font-weight: bold;
    line-height: 1.55;
}

.subbrand {
    font-size: 8pt;
    letter-spacing: 1px;
}

.issue-line {
    font-size: 9pt;
    text-align: center;
    border-bottom: 1px solid #2b241b;
    padding: 5px 0 7px;
}

.column-label {
    color: #fff;
    background: #792e24;
    padding: 6px;
    font-size: 10pt;
    text-align: center;
    font-weight: bold;
}

.story {
    padding: 10px 0;
    border-bottom: 1px solid #a89d8c;
}

.story:last-child {
    border-bottom: none;
}

.category {
    color: #8d3025;
    font-size: 9pt;
    font-weight: bold;
    margin: 0 0 4px;
}

h1 {
    font-size: 20pt;
    line-height: 1.5;
    margin: 4px 0 10px;
}

h2 {
    font-size: 15pt;
    line-height: 1.45;
    margin: 4px 0 8px;
}

.story-photo {
    display: block;
    max-width: 100%;
    max-height: 95mm;
    margin-bottom: 6px;
}

p {
    font-size: 10pt;
    line-height: 1.65;
    text-align: justify;
    margin: 6px 0;
}

.story a.more {
    color: #792e24;
    font-size: 9pt;
    font-weight: bold;
}

.meta {
    font-size: 8pt;
    color: #6c6056;
    margin-top: 4px;
}

.footer {
    margin-top: 12px;
    border-top: 2px double #2b241b;
    padding-top: 8px;
    font-size: 8pt;
    text-align: center;
}

</style>
</head>

<body>

<div class="masthead">

    <div class="subbrand">
        DAILY DARIYANAGAR • E-PAPER
    </div>

    <div class="brand">
        {{ config('epaper.name_' . $lang) }}
    </div>

    <div class="subbrand">
        {{ config('epaper.strapline_' . $lang) }}
    </div>

</div>


<div class="issue-line">

    {{ \Carbon\CarbonImmutable::parse($edition['date'])
        ->locale($lang)
        ->translatedFormat('d F Y')
    }}

    &nbsp; • &nbsp;

    {{ $lang === 'bn' ? 'পৃষ্ঠা' : 'PAGE' }}
    {{ $page['number'] }}

    &nbsp; • &nbsp;

    {{ $lang === 'bn' ? 'কলাম' : 'COLUMN' }}
    {{ $columnNumber }}

</div>


<div class="column-label">

    {{ $lang === 'bn'
        ? 'সংবাদপত্রের স্বতন্ত্র কলাম'
        : 'INDIVIDUAL NEWSPAPER COLUMN'
    }}

</div>


<div class="column-border">

    @foreach($stories as $post)

        <div class="story">

            <div class="category">
                {{ $epaper->category($post, $lang) }}
            </div>


            @if(
                $page['kind'] === 'cover'
                && $columnNumber === 1
            )

                <h1>

                    <a href="{{ $epaper->storyUrl($post, $lang) }}">
                        {{ $epaper->title($post, $lang) }}
                    </a>

                </h1>

            @else

                <h2>

                    <a href="{{ $epaper->storyUrl($post, $lang) }}">
                        {{ $epaper->title($post, $lang) }}
                    </a>

                </h2>

            @endif


            @php
                $imagePath = $epaper->image($post, true);
            @endphp


            @if(
                $imagePath
                && (
                    $page['kind'] !== 'cover'
                    || $columnNumber === 1
                )
            )

                <img
                    class="story-photo"
                    src="{{ $imagePath }}"
                    alt="{{ $epaper->title($post, $lang) }}"
                >

                <div class="meta">

                    {{ $lang === 'bn'
                        ? 'সংযুক্ত ছবি'
                        : 'Attached image'
                    }}

                </div>

            @endif


            <p>

                {{ $epaper->summary(
                    $post,
                    $lang,
                    $page['kind'] === 'cover'
                        ? ($columnNumber === 1 ? 720 : 205)
                        : 440
                ) }}

            </p>


            <a
                class="more"
                href="{{ $epaper->storyUrl($post, $lang) }}"
            >

                {{ $lang === 'bn'
                    ? 'সম্পূর্ণ প্রতিবেদন পড়ুন'
                    : 'READ FULL STORY'
                }} ›

            </a>

        </div>

    @endforeach

</div>


<div class="footer">

    {{ $lang === 'bn'
        ? 'ডেইলি দরিয়ানগর ডিজিটাল ই-পেপার'
        : 'Daily Dariyanagar Digital E-Paper'
    }}

    • {{ $edition['date'] }}
    • {{ $page['number'] }} / {{ $columnNumber }}

</div>

</body>
</html>