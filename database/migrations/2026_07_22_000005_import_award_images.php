<?php

use App\Models\Award;
use Illuminate\Database\Migrations\Migration;

/**
 * Populate the "Pencapaian & Penghargaan" popup awards (2013–2026) from the
 * per-year image import. The image files ship in the repo under
 * storage/app/public/awards/{year}/; this migration creates the matching Award
 * rows on every environment (deploy runs migrations, not seeders or the
 * awards:import-local command). Keyed on image path → idempotent, and it does
 * not touch the existing hero/placeholder awards.
 */
return new class extends Migration
{
    public function up(): void
    {
        $file = database_path('data/awards.json');

        if (! is_file($file)) {
            return;
        }

        $rows = json_decode(file_get_contents($file), true) ?: [];

        foreach ($rows as $i => $row) {
            if (empty($row['image'])) {
                continue;
            }

            Award::updateOrCreate(
                ['image' => $row['image']],
                [
                    'title_id' => $row['title_id'] ?? null,
                    'title_en' => $row['title_en'] ?? null,
                    'year' => $row['year'] ?? null,
                    'is_hero' => false,
                    'sort' => 1000 + $i,
                ]
            );
        }
    }

    public function down(): void
    {
        Award::where('image', 'like', 'awards/%')->delete();
    }
};
