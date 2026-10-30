<!DOCTYPE html>
<html lang="{{ $lang }}">

<head>

<meta charset="UTF-8">

<style>

body {
    font-family: dariyaserif;
    color: #241e17;
    background: #fff;
    font-size: 10pt;
}

a {
    color: #241e17;
    text-decoration: none;
}

.ep-sheet {
    width: 100%;
    font-family: dariyaserif;
    background: #fff;
}

.ep-issue-topline,
.ep-issue-meta,
.ep-paper-footer {
    font-size: 8pt;
    padding: 4px 0;
    border-bottom: 1px solid #36302a;
}

.ep-issue-topline span,
.ep-issue-meta span,
.ep-paper-footer span {
    display: inline-block;
    width: 32%;
    text-align: center;
}

.ep-masthead {
    text-align: center;
    border-bottom: 3px double #25201b;
    padding: 8px 0 12px;
}

.ep-masthead-ornament {
    font-size: 8pt;
    letter-spacing: 3px;
}

.ep-masthead-name {
    font-size: 42pt;
    font-weight: bold;
    line-height: 1.45;
}

.ep-masthead-sub {
    font-size: 10pt;
}

.ep-issue-meta {
    border-bottom: 2px solid #25201b;
    margin-bottom: 10px;
}

.ep-cover-kicker,
.ep-section-banner {
    background: #792e24;
    color: white;
    text-align: center;
    padding: 5px;
    font-weight: bold;
    font-size: 10pt;
}

.ep-cover-headline {
    font-size: 27pt;
    text-align: center;
    font-weight: bold;
    line-height: 1.5;
    padding: 8px 15px 2px;
    margin: 0;
}

.ep-cover-deck {
    text-align: center;
    font-size: 11pt;
    border-bottom: 2px solid #29231e;
    margin-bottom: 10px;
    padding: 5px 14px 8px;
}

.ep-lead-table,
.ep-story-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.ep-lead-table td {
    vertical-align: top;
    padding: 7px;
}

.ep-cover-main {
    width: 60%;
    border-right: 1px solid #5b544a;
}

.ep-cover-rail {
    width: 40%;
}

.ep-cover-photo {
    width: 100%;
    max-height: 95mm;
}

.ep-photo-caption {
    color: #666;
    font-size: 7pt;
    margin: 3px 0;
}

.ep-story-category {
    color: #8c3024;
    font-size: 9pt;
    font-weight: bold;
    margin: 3px 0;
}

.ep-cover-body p {
    font-size: 10pt;
    line-height: 1.6;
    text-align: justify;
}

.ep-rail-story {
    padding: 6px 0;
    border-bottom: 1px solid #7f786c;
}

.ep-rail-story h2 {
    font-size: 14pt;
    line-height: 1.4;
    margin: 3px 0;
}

.ep-rail-story p {
    font-size: 9pt;
    line-height: 1.5;
    margin: 3px 0;
}

.ep-section-banner {
    text-align: left;
    margin-bottom: 9px;
}

.ep-section-banner small {
    float: right;
    font-size: 7pt;
}

.ep-story-cell {
    width: 50%;
    padding: 9px;
    border-right: 1px solid #8a8174;
    border-bottom: 1px solid #8a8174;
    vertical-align: top;
}

.ep-story-cell h2 {
    font-size: 15pt;
    line-height: 1.45;
    margin: 4px 0 6px;
}

.ep-story-cell p {
    font-size: 9pt;
    line-height: 1.55;
    text-align: justify;
}

.ep-story-photo {
    width: 100%;
    max-height: 40mm;
}

.ep-read-link {
    font-size: 8pt;
    color: #792e24;
    font-weight: bold;
}

.ep-paper-footer {
    border-top: 2px double #25201b;
    border-bottom: 0;
    margin-top: 10px;
}

</style>

</head>

<body>

@foreach($pagesToPrint as $page)

    @include('epaper.sheet', [
        'page' => $page,
        'forPdf' => true,
    ])

    @if(!$loop->last)
        <pagebreak />
    @endif

@endforeach

</body>
</html>