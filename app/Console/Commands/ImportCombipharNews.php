<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCombipharNews extends Command
{
    protected $signature = 'news:import-combiphar';

    protected $description = 'Import articles (Info Kesehatan + Siaran Pers) from combiphar.com into the CMS.';

    /** Remote article category id (combiphar.com) => our Article.category. */
    private const MAP = [
        1 => 'edukasi_gaya_hidup',   // Info Kesehatan  -> Health Information
        2 => 'pembaruan_korporasi',  // Siaran Pers     -> Corporate Update
    ];

    public function handle(): int
    {
        foreach (self::MAP as $remoteId => $category) {
            $page = 1;
            $count = 0;

            do {
                $response = Http::acceptJson()->timeout(30)->get('https://www.combiphar.com/back/api/v1/articles', [
                    'locale' => 'id',
                    'articleCategoryId' => $remoteId,
                    'page' => $page,
                    'pageSize' => 25,
                ]);

                $paginator = data_get($response->json(), 'data.articles', []);
                $items = $paginator['data'] ?? [];
                $lastPage = (int) ($paginator['last_page'] ?? 1);

                foreach ($items as $a) {
                    $title = trim($a['title'] ?? '');
                    if ($title === '') {
                        continue;
                    }
                    $slug = ! empty($a['slug']) ? $a['slug'] : Str::slug($title);
                    $excerpt = trim(strip_tags(str_replace('&nbsp;', ' ', $a['og_description'] ?? '')));
                    $body = $a['body'] ?? '';
                    $date = ! empty($a['display_date']) ? Carbon::parse($a['display_date']) : now();

                    Article::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'title_id' => $title,
                            'title_en' => $title,
                            'excerpt_id' => $excerpt,
                            'excerpt_en' => $excerpt,
                            'body_id' => $body,
                            'body_en' => $body,
                            'category' => $category,
                            'cover_image' => $this->downloadImage($a['image'] ?? null),
                            'published_at' => $date,
                        ]
                    );
                    $count++;
                }

                $page++;
            } while ($page <= $lastPage);

            $this->info("{$category}: imported/updated {$count} articles (remote category {$remoteId}).");
        }

        return self::SUCCESS;
    }

    /** Download a remote article image into storage/app/public/articles; returns the stored path (or the URL on failure). */
    private function downloadImage(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $filename = basename((string) parse_url($url, PHP_URL_PATH));
        if ($filename === '') {
            return $url;
        }
        $path = 'articles/'.$filename;

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        try {
            $response = Http::timeout(30)->get($url);
            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable $e) {
            $this->warn("Image download failed for {$url}: {$e->getMessage()}");
        }

        return $url; // fallback: hotlink (PageController::img() passes absolute URLs through)
    }
}
