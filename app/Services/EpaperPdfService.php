<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use RuntimeException;

class EpaperPdfService
{
    public function __construct(
        private readonly EpaperService $epaper
    ) {
    }

    /**
     * Full edition:
     * $page = null, $column = null (A3)
     *
     * Individual newspaper page:
     * $page = N, $column = null (A3)
     *
     * Individual newspaper column:
     * $page = N, $column = 1 or 2 (A4)
     *
     * Returns a path relative to Laravel's private local disk.
     */
    public function generate(
        array $edition,
        string $lang = 'bn',
        ?int $page = null,
        ?int $column = null
    ): string {

        abort_unless(
            in_array($lang, ['bn', 'en'], true),
            404
        );

        $pages = array_values(
            $edition['pages'] ?? []
        );

        abort_if(
            empty($pages),
            404,
            'This edition has no published pages.'
        );

        abort_if(
            $column !== null && $page === null,
            404,
            'Select a page first.'
        );

        /*
        |--------------------------------------------------------------------------
        | Validate newspaper page number
        |--------------------------------------------------------------------------
        */

        if ($page !== null) {

            abort_unless(
                $page >= 1 &&
                $page <= count($pages),
                404
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Validate column number
        |--------------------------------------------------------------------------
        */

        if ($column !== null) {

            abort_unless(
                in_array($column, [1, 2], true),
                404
            );

        }

        $selectedPage = $page !== null
            ? $pages[$page - 1]
            : null;

        /*
        |--------------------------------------------------------------------------
        | Find stories belonging to the selected column
        |--------------------------------------------------------------------------
        */

        $columnStories = $column !== null
            ? $this->storiesInColumn(
                $selectedPage,
                $column
            )
            : collect();

        if ($column !== null) {

            abort_if(
                $columnStories->isEmpty(),
                404,
                'This column has no articles.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Determine download type
        |--------------------------------------------------------------------------
        */

        $scope = $column !== null
            ? "page-{$page}-column-{$column}"
            : (
                $page !== null
                    ? "page-{$page}"
                    : 'full-edition'
            );

        /*
        |--------------------------------------------------------------------------
        | Unique PDF cache version
        |--------------------------------------------------------------------------
        |
        | The new font-version suffix prevents Laravel from
        | returning old PDFs generated with previous settings.
        |--------------------------------------------------------------------------
        */

        $version = 'bundled-freeserif-bn-2026-10-v3';

        $fingerprint = sha1(
            $this->epaper->cacheKey(
                $edition,
                $lang
            )
            . '|'
            . $version
        );

        $relativePath = sprintf(
            'epaper/%s/%s-%s-%s.pdf',
            $edition['date'],
            $scope,
            $lang,
            $fingerprint
        );

        $disk = Storage::disk('local');

        /*
        |--------------------------------------------------------------------------
        | Return existing generated PDF
        |--------------------------------------------------------------------------
        */

        if ($disk->exists($relativePath)) {

            return $relativePath;

        }

        /*
        |--------------------------------------------------------------------------
        | Create mPDF with compatible Bengali font
        |--------------------------------------------------------------------------
        */

        $mpdf = $this->makeMpdf(
            $column !== null
        );

        $mpdf->SetTitle(
            "Daily Dariyanagar | {$edition['date']} | {$scope}"
        );

        $mpdf->SetAuthor(
            'Daily Dariyanagar'
        );

        $mpdf->SetCreator(
            'Daily Dariyanagar E-Paper'
        );

        $mpdf->showImageErrors = false;

        /*
        |--------------------------------------------------------------------------
        | Render single-column PDF
        |--------------------------------------------------------------------------
        */

        if ($column !== null) {

            $html = view(
                'epaper.column-pdf',
                [
                    'edition' => $edition,

                    'page' => $selectedPage,

                    'columnNumber' => $column,

                    'stories' => $columnStories,

                    'lang' => $lang,

                    'epaper' => $this->epaper,
                ]
            )->render();

        } else {

            /*
            |--------------------------------------------------------------------------
            | Render full-edition or individual-page PDF
            |--------------------------------------------------------------------------
            */

            $html = view(
                'epaper.pdf',
                [
                    'edition' => $edition,

                    'pagesToPrint' => $selectedPage !== null
                        ? [$selectedPage]
                        : $pages,

                    'lang' => $lang,

                    'epaper' => $this->epaper,
                ]
            )->render();

        }

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        $mpdf->WriteHTML($html);

        $pdfBytes = $mpdf->Output(
            '',
            'S'
        );

        /*
        |--------------------------------------------------------------------------
        | Save privately
        |--------------------------------------------------------------------------
        */

        if (
            $disk->put(
                $relativePath,
                $pdfBytes
            ) === false
        ) {

            throw new RuntimeException(
                'Unable to save the E-Paper PDF. Check storage permissions.'
            );

        }

        return $relativePath;
    }

    /*
    |--------------------------------------------------------------------------
    | Get articles from selected newspaper column
    |--------------------------------------------------------------------------
    |
    | Front page:
    | Column 1 = main headline and lead report
    | Column 2 = other front-page stories
    |
    | Inside pages:
    | Column 1 = article positions 1, 3, 5
    | Column 2 = article positions 2, 4, 6
    |--------------------------------------------------------------------------
    */

    private function storiesInColumn(
        array $page,
        int $column
    ): Collection {

        $stories = $page['stories']->values();

        if ($page['kind'] === 'cover') {

            return $column === 1
                ? $stories->take(1)->values()
                : $stories->skip(1)->values();

        }

        return $stories
            ->filter(
                fn ($story, $index) =>
                    $index % 2 === $column - 1
            )
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | FIXED mPDF FONT CONFIGURATION
    |--------------------------------------------------------------------------
    |
    | The earlier Noto Serif Bengali fonts triggered:
    |
    | GPOS Lookup Type 5, Format 3 not supported
    |
    | This uses mPDF's bundled FreeSerif font instead.
    |
    | FreeSerif Regular supports Bengali characters.
    |
    | Both R and B use the Regular font to prevent
    | missing Bengali glyphs in headlines.
    |
    | useOTL = 0x80 enables OpenType shaping
    | for Bengali and other complex scripts.
    |--------------------------------------------------------------------------
    */

    private function makeMpdf(
        bool $singleColumn
    ): Mpdf {

        /*
        |--------------------------------------------------------------------------
        | Temporary font metrics and PDF files
        |--------------------------------------------------------------------------
        */

        $tempDirectory = storage_path(
            'app/mpdf-temp'
        );

        File::ensureDirectoryExists(
            $tempDirectory
        );

        /*
        |--------------------------------------------------------------------------
        | Load bundled mPDF fonts
        |--------------------------------------------------------------------------
        */

        $fontVariables = (
            new FontVariables()
        )->getDefaults();

        /*
        |--------------------------------------------------------------------------
        | Create PDF renderer
        |--------------------------------------------------------------------------
        */

        return new Mpdf([

            'mode' => 'utf-8',

            /*
            | A3 = full newspaper/page
            | A4 = individual column
            */

            'format' => $singleColumn
                ? 'A4'
                : 'A3',

            'orientation' => 'P',

            'margin_left' => $singleColumn
                ? 13
                : 8,

            'margin_right' => $singleColumn
                ? 13
                : 8,

            'margin_top' => $singleColumn
                ? 13
                : 8,

            'margin_bottom' => $singleColumn
                ? 13
                : 8,

            'tempDir' => $tempDirectory,

            /*
            |--------------------------------------------------------------------------
            | Bengali-compatible font alias
            |--------------------------------------------------------------------------
            |
            | Keep the same "dariyaserif" name used in
            | epaper/pdf.blade.php and epaper/column-pdf.blade.php.
            |
            | No additional font downloads are required.
            |--------------------------------------------------------------------------
            */

            'fontdata' => array_replace(
                $fontVariables['fontdata'],
                [

                    'dariyaserif' => [

                        'R' => 'FreeSerif.ttf',

                        'B' => 'FreeSerif.ttf',

                        'I' => 'FreeSerifItalic.ttf',

                        'BI' => 'FreeSerifItalic.ttf',

                        'useOTL' => 0x80,

                    ],

                ]
            ),

            /*
            |--------------------------------------------------------------------------
            | Default font
            |--------------------------------------------------------------------------
            */

            'default_font' => 'dariyaserif',

            /*
            |--------------------------------------------------------------------------
            | Language and script handling
            |--------------------------------------------------------------------------
            */

            'autoScriptToLang' => true,

            'autoLangToFont' => false,

        ]);
    }
}
