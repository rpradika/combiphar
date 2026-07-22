<?php

namespace App\Console\Commands;

use App\Models\Award;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportLocalAwards extends Command
{
    protected $signature = 'awards:import-local
        {--path= : Folder holding the {year}-*.zip archives (default: base_path/award)}
        {--dry-run : List what would be imported without writing files or rows}';

    protected $description = 'Import award images from per-year zip archives (award/{year}-*.zip) into the Award CMS records for the "Pencapaian & Penghargaan" popup.';

    /** Image extensions we import (the popup renders <img>); everything else (PDF, …) is skipped. */
    private const IMAGE_EXT = ['jpg', 'jpeg', 'png', 'webp'];

    public function handle(): int
    {
        $dir = $this->option('path') ?: base_path('award');

        if (! is_dir($dir)) {
            $this->error("Award folder not found: {$dir}");

            return self::FAILURE;
        }

        $zips = glob(rtrim($dir, '/\\').'/*.zip') ?: [];
        if (! $zips) {
            $this->error("No .zip archives found in {$dir}");

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $sort = (int) (Award::max('sort') ?? 0);
        $imported = 0;
        $skipped = 0;

        foreach ($zips as $zipPath) {
            $year = $this->yearFromZipName(basename($zipPath));

            $zip = new \ZipArchive;
            if ($zip->open($zipPath) !== true) {
                $this->warn('Could not open '.basename($zipPath).' — skipped.');

                continue;
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'] ?? '';

                // Skip directory entries and macOS resource-fork junk.
                if ($name === '' || str_ends_with($name, '/') || str_starts_with($name, '__MACOSX/')) {
                    continue;
                }

                $base = basename($name);
                if ($base === '' || str_starts_with($base, '.')) {
                    continue;
                }

                $ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));
                if (! in_array($ext, self::IMAGE_EXT, true)) {
                    $skipped++;        // PDFs / certificates / other non-images
                    continue;
                }

                // Prefer the year from the entry's top folder (e.g. "2013/IMG.jpg"); fall back to the zip name.
                $entryYear = $this->yearFromEntry($name) ?? $year;

                // Deterministic destination so re-runs update the same row instead of duplicating.
                $slug = Str::slug(pathinfo($base, PATHINFO_FILENAME)) ?: 'award';
                $dest = "awards/{$entryYear}/{$slug}-".substr(md5($name), 0, 6).'.'.$ext;

                [$titleId, $titleEn] = $this->titlesFor($base, $entryYear);

                if ($dry) {
                    $this->line("[{$entryYear}] {$titleId}  ←  {$name}");
                    $imported++;

                    continue;
                }

                $bytes = $zip->getFromIndex($i);
                if ($bytes === false) {
                    $this->warn("Could not read {$name} — skipped.");
                    $skipped++;

                    continue;
                }

                Storage::disk('public')->put($dest, $bytes);

                Award::updateOrCreate(
                    ['image' => $dest],
                    [
                        'title_id' => $titleId,
                        'title_en' => $titleEn,
                        'year' => $entryYear,
                        'is_hero' => false,
                        'sort' => ++$sort,
                    ]
                );
                $imported++;
            }

            $zip->close();
        }

        $verb = $dry ? 'Would import' : 'Imported/updated';
        $this->info("{$verb} {$imported} award images; skipped {$skipped} non-image files (PDFs, etc.).");

        if (! $dry) {
            $this->line('Next: run `php artisan images:optimize` to shrink the large photos, then check the About page popup.');
        }

        return self::SUCCESS;
    }

    /** "2013-20260722T....zip" → "2013". */
    private function yearFromZipName(string $file): string
    {
        return preg_match('/^(\d{4})/', $file, $m) ? $m[1] : 'unknown';
    }

    /** "2013/IMG_8119.JPG" → "2013" when the top segment is a 4-digit year. */
    private function yearFromEntry(string $entry): ?string
    {
        $top = explode('/', $entry)[0];

        return preg_match('/^\d{4}$/', $top) ? $top : null;
    }

    /**
     * Build a bilingual caption. Descriptive filenames become the title; generic
     * camera names (IMG_8119, CML_7910, DSC0001) get a neutral "Penghargaan {year}".
     *
     * @return array{0:string,1:string}
     */
    private function titlesFor(string $base, string $year): array
    {
        $name = pathinfo($base, PATHINFO_FILENAME);

        // Common double extension in the source (e.g. "CML_7910.jpg.jpeg").
        $name = preg_replace('/\.(jpg|jpeg|png)$/i', '', $name);

        // Generic camera/export names carry no real title.
        if (preg_match('/^(IMG|CML|DSC|DSCF|PICT|photo|image)[\s_-]*\d+/i', trim($name)) || preg_match('/^\d+$/', trim($name))) {
            return ["Penghargaan {$year}", "Award {$year}"];
        }

        // Clean a descriptive filename into a readable caption.
        $t = str_replace('_', ' ', $name);
        $t = preg_replace('/^[^\p{L}\p{N}]+/u', '', $t);   // drop leading "· ", bullets, stray punctuation
        $t = preg_replace('/\s*\(\d+\)\s*$/', '', $t);     // drop trailing " (1)" duplicate markers
        $t = trim(preg_replace('/\s+/', ' ', $t));

        if ($t === '') {
            return ["Penghargaan {$year}", "Award {$year}"];
        }

        return [$t, $t];
    }
}
