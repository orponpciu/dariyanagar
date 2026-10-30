<?php

namespace App\Http\Controllers;

use App\Services\EpaperPdfService;
use App\Services\EpaperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EpaperController extends Controller
{
    public function index(
        Request $request,
        EpaperService $epaper
    ): View {
        $lang = $request->query('lang') === 'en' ? 'en' : 'bn';

        $date = $request->query('date');
        $date = is_string($date) && $date !== '' ? $date : null;

        $edition = $epaper->build($date);

        $pageNumber = max(1, (int) $request->query('page', 1));

        if (count($edition['pages'])) {
            $pageNumber = min($pageNumber, count($edition['pages']));
        }

        return view('epaper.index', compact(
            'edition',
            'epaper',
            'lang',
            'pageNumber'
        ));
    }

    public function download(
        Request $request,
        EpaperService $epaper,
        EpaperPdfService $pdf
    ): StreamedResponse {
        $lang = $request->query('lang') === 'en' ? 'en' : 'bn';

        $date = $request->query('date');
        $date = is_string($date) && $date !== '' ? $date : null;

        $edition = $epaper->build($date);

        $filePath = $pdf->generate($edition, $lang);

        $name = "Daily-Dariyanagar-{$edition['date']}-full-edition-{$lang}.pdf";

        return Storage::disk('local')->download(
            $filePath,
            $name,
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function downloadPage(
        Request $request,
        int $page,
        EpaperService $epaper,
        EpaperPdfService $pdf
    ): StreamedResponse {
        $lang = $request->query('lang') === 'en' ? 'en' : 'bn';

        $date = $request->query('date');
        $date = is_string($date) && $date !== '' ? $date : null;

        $edition = $epaper->build($date);

        $filePath = $pdf->generate($edition, $lang, $page);

        $name = "Daily-Dariyanagar-{$edition['date']}-page-{$page}-{$lang}.pdf";

        return Storage::disk('local')->download(
            $filePath,
            $name,
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function downloadColumn(
        Request $request,
        int $page,
        int $column,
        EpaperService $epaper,
        EpaperPdfService $pdf
    ): StreamedResponse {
        $lang = $request->query('lang') === 'en' ? 'en' : 'bn';

        $date = $request->query('date');
        $date = is_string($date) && $date !== '' ? $date : null;

        $edition = $epaper->build($date);

        $filePath = $pdf->generate($edition, $lang, $page, $column);

        $name = "Daily-Dariyanagar-{$edition['date']}-page-{$page}-column-{$column}-{$lang}.pdf";

        return Storage::disk('local')->download(
            $filePath,
            $name,
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}