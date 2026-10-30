
@php
    $isCover = $page['kind'] === 'cover';

    $dateText = \Carbon\CarbonImmutable::parse(
        $edition['date']
    )
        ->locale($lang)
        ->translatedFormat('l, d F Y');

    $number = fn ($n) => $lang === 'bn'
        ? strtr(
            (string) $n,
            [
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
            ]
        )
        : (string) $n;

    /*
    | PDF: embedded images
    | Website: existing public storage image URL
    */

    $imageFor = fn ($post) => isset($images)
        ? ($images[$post->id] ?? null)
        : $epaper->image($post, false);

    $linkFor = fn ($post) =>
        $epaper->storyUrl($post, $lang);
@endphp


<article
    class="ep-sheet {{ $isCover ? 'ep-sheet-cover' : 'ep-sheet-inside' }}"
>

    <!-- TOP NEWSPAPER STRIP -->

    <div class="ep-issue-topline">

        <span>
            {{ $lang === 'bn'
                ? 'স্বাধীন সংবাদ • প্রতিদিন'
                : 'INDEPENDENT DAILY'
            }}
        </span>

        <span>
            {{ $lang === 'bn'
                ? 'চট্টগ্রাম, বাংলাদেশ'
                : 'CHATTOGRAM, BANGLADESH'
            }}
        </span>

        <span>
            {{ $lang === 'bn'
                ? 'ডিজিটাল সংস্করণ'
                : 'DIGITAL EDITION'
            }}
        </span>

    </div>


    <!-- VINTAGE BENGALI MASTHEAD -->

    <header class="ep-masthead">

        <div class="ep-masthead-ornament">
            ✦ &nbsp; DAILY NEWSPAPER &nbsp; ✦
        </div>

        <div class="ep-masthead-name">

            {{ config(
                'epaper.name_' . $lang,
                $lang === 'bn'
                    ? 'ডেইলি দরিয়ানগর'
                    : 'DAILY DARIYANAGAR'
            ) }}

        </div>

        <div class="ep-masthead-sub">

            {{ config('epaper.strapline_' . $lang) }}

        </div>

    </header>


    <!-- DATE / ISSUE / PAGE STRIP -->

    <div class="ep-issue-meta">

        <span>
            {{ $dateText }}
        </span>

        <span>
            {{ $lang === 'bn'
                ? 'জনগণের কথা • সত্যের সন্ধানে'
                : 'TRUTH • COMMUNITY • REPORTING'
            }}
        </span>

        <span>
            {{ $lang === 'bn' ? 'পৃষ্ঠা' : 'PAGE' }}

            {{ $number($page['number']) }}

            /

            {{ $number(count($edition['pages'])) }}
        </span>

    </div>


    @if($isCover)

        <!-- FRONT PAGE -->

        @php
            $lead = $page['stories']->first();
        @endphp

        @if($lead)

            <div class="ep-cover-kicker">

                {{ $lang === 'bn'
                    ? 'প্রধান সংবাদ'
                    : 'LEAD NEWS'
                }}

            </div>


            <!-- MAIN HEADLINE -->

            <h1 class="ep-cover-headline">

                <a href="{{ $linkFor($lead) }}">

                    {{ $epaper->title($lead, $lang) }}

                </a>

            </h1>


            <!-- LEAD STORY SUBHEADLINE -->

            <p class="ep-cover-deck">

                {{ $epaper->summary(
                    $lead,
                    $lang,
                    260
                ) }}

            </p>


            <!-- MAIN TWO-COLUMN NEWS AREA -->

            <table
                class="ep-lead-table"
                role="presentation"
            >

                <tbody>

                    <tr>

                        <!-- FIRST COLUMN: LEAD STORY -->

                        <td class="ep-cover-main">

                            @if($imageFor($lead))

                                <a href="{{ $linkFor($lead) }}">

                                    <img
                                        class="ep-cover-photo"
                                        src="{{ $imageFor($lead) }}"
                                        alt="{{ $epaper->title($lead, $lang) }}"
                                    >

                                </a>

                                <div class="ep-photo-caption">

                                    {{ $lang === 'bn'
                                        ? 'ছবি: ডেইলি দরিয়ানগর'
                                        : 'Photo: Daily Dariyanagar'
                                    }}

                                </div>

                            @endif


                            <div class="ep-cover-body">

                                <div class="ep-story-category">

                                    {{ $epaper->category(
                                        $lead,
                                        $lang
                                    ) }}

                                </div>


                                <p>

                                    {{ $epaper->summary(
                                        $lead,
                                        $lang,
                                        680
                                    ) }}

                                </p>


                                <a
                                    class="ep-read-link"
                                    href="{{ $linkFor($lead) }}"
                                >

                                    {{ $lang === 'bn'
                                        ? 'সম্পূর্ণ সংবাদ'
                                        : 'FULL STORY'
                                    }}

                                    ↗

                                </a>

                            </div>

                        </td>


                        <!-- SECOND COLUMN: OTHER NEWS -->

                        <td class="ep-cover-rail">

                            @foreach(
                                $page['stories']->skip(1)
                                as $news
                            )

                                <section class="ep-rail-story">

                                    <div class="ep-story-category">

                                        {{ $epaper->category(
                                            $news,
                                            $lang
                                        ) }}

                                    </div>


                                    <h2>

                                        <a href="{{ $linkFor($news) }}">

                                            {{ $epaper->title(
                                                $news,
                                                $lang
                                            ) }}

                                        </a>

                                    </h2>


                                    <p>

                                        {{ $epaper->summary(
                                            $news,
                                            $lang,
                                            185
                                        ) }}

                                    </p>


                                    <a
                                        class="ep-read-link"
                                        href="{{ $linkFor($news) }}"
                                    >

                                        {{ $lang === 'bn'
                                            ? 'বিস্তারিত'
                                            : 'READ MORE'
                                        }}

                                        ›

                                    </a>

                                </section>

                            @endforeach

                        </td>

                    </tr>

                </tbody>

            </table>

        @endif


    @else

        <!-- INSIDE NEWSPAPER PAGES -->

        <div class="ep-section-banner">

            <span>

                {{ $lang === 'bn'
                    ? 'দেশ-বিদেশ • সংবাদ ও প্রতিবেদন'
                    : 'REPORTS • FEATURES • WORLD'
                }}

            </span>


            <small>
                {{ $dateText }}
            </small>

        </div>


        <table
            class="ep-story-table"
            role="presentation"
        >

            <tbody>

                @foreach(
                    $page['stories']->chunk(2)
                    as $row
                )

                    <tr>

                        @foreach($row as $news)

                            <td class="ep-story-cell">

                                <div class="ep-story-category">

                                    {{ $epaper->category(
                                        $news,
                                        $lang
                                    ) }}

                                </div>


                                <h2>

                                    <a href="{{ $linkFor($news) }}">

                                        {{ $epaper->title(
                                            $news,
                                            $lang
                                        ) }}

                                    </a>

                                </h2>


                                @if($imageFor($news))

                                    <a href="{{ $linkFor($news) }}">

                                        <img
                                            class="ep-story-photo"
                                            src="{{ $imageFor($news) }}"
                                            alt="{{ $epaper->title($news, $lang) }}"
                                        >

                                    </a>

                                @endif


                                <p>

                                    {{ $epaper->summary(
                                        $news,
                                        $lang,
                                        340
                                    ) }}

                                </p>


                                <a
                                    class="ep-read-link"
                                    href="{{ $linkFor($news) }}"
                                >

                                    {{ $lang === 'bn'
                                        ? 'বিস্তারিত প্রতিবেদন'
                                        : 'READ ARTICLE'
                                    }}

                                    ›

                                </a>

                            </td>

                        @endforeach


                        @if($row->count() === 1)

                            <td
                                class="ep-story-cell ep-empty-cell"
                            ></td>

                        @endif

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif


    <!-- BOTTOM NEWSPAPER PAGE RULE -->

    <footer class="ep-paper-footer">

        <span>

            {{ config(
                'epaper.name_en',
                'DAILY DARIYANAGAR'
            ) }}

            • E-PAPER

        </span>


        <span>

            {{ $lang === 'bn'
                ? 'শুধু ডিজিটাল সংবাদসংকলন'
                : 'DIGITAL NEWSPAPER DIGEST'
            }}

        </span>


        <span>
            {{ $number($page['number']) }}
        </span>

    </footer>

</article>
