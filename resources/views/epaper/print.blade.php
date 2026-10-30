
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

/*
| Shared newspaper styles.
| The PDF and browser use the same paper design.
*/

{!! $newspaperCss !!}

@page {
    size: A3 portrait;
    margin: 0;
}

html,
body {
    margin: 0;
    padding: 0;
    background: #fff;
}

.ep-sheet {
    width: 297mm;
    height: 419mm;
    min-height: 0;
    max-width: none;

    padding: 10mm 11mm 9mm;

    margin: 0;

    box-shadow: none;

    page-break-after: always;
    break-after: page;

    overflow: hidden;
}

.ep-sheet:last-child {
    page-break-after: auto;
    break-after: auto;
}

.ep-masthead-name {
    font-size: 41pt;
}

.ep-cover-headline {
    font-size: 24pt;
}

.ep-cover-photo {
    max-height: 108mm;
}

.ep-rail-story h2 {
    font-size: 13.5pt;
}

.ep-rail-story p {
    font-size: 9pt;
}

.ep-story-cell h2 {
    font-size: 15pt;
}

.ep-story-cell p {
    font-size: 9pt;
}

</style>

</head>

<body>

    @foreach($pagesToPrint as $page)

        @include('epaper.sheet', [
            'page' => $page,
            'images' => $images,
        ])

    @endforeach

</body>
</html>
