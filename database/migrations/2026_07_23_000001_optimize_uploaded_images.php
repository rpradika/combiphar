<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * One-shot per environment: shrink oversized CMS uploads in storage/app/public.
 * Storage is per-env (gitignored), so the dev server's own uploads (CSR gallery
 * photos etc.) were never touched by the local `images:optimize` runs — riding
 * the deploy's `migrate --force` is the only automated path onto that box.
 * Idempotent: already-optimized files are skipped (min-kb threshold / "no win").
 */
return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('images:optimize');

        // The optimizer converted two TRACKED seed PNGs to JPG. On a deploy the
        // git reset delivers them already as .jpg BEFORE migrate runs, so the
        // conversion (and its automatic DB-ref rewrite) never fires there —
        // fix the refs explicitly. Idempotent: no-op where already correct.
        foreach ([
            'seed/product-consumer-health.png' => 'seed/product-consumer-health.jpg',
            'seed/product-nutrition-herbal-care.png' => 'seed/product-nutrition-herbal-care.jpg',
        ] as $old => $new) {
            $this->replaceRefs($old, $new);
        }
    }

    private function replaceRefs(string $old, string $new): void
    {
        $columns = DB::select(
            "SELECT TABLE_NAME t, COLUMN_NAME c FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND DATA_TYPE IN ('varchar', 'text', 'mediumtext', 'longtext', 'json')
               AND TABLE_NAME NOT IN ('migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs')"
        );

        foreach ($columns as $col) {
            foreach ([[$old, $new], [str_replace('/', '\\/', $old), str_replace('/', '\\/', $new)]] as [$from, $to]) {
                DB::update(
                    "UPDATE `{$col->t}` SET `{$col->c}` = REPLACE(`{$col->c}`, ?, ?) WHERE `{$col->c}` LIKE ?",
                    [$from, $to, '%'.$from.'%'],
                );
            }
        }
    }

    public function down(): void
    {
        // Lossy in-place rewrite — nothing to restore.
    }
};
