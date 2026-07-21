<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill slugs for CSR programs that have neither a slug nor an external
     * link, so their card's "Selengkapnya" button links to a /csr/{slug} detail
     * page instead of being dead (e.g. Tennis, Football, Technical Sport).
     * Rows with an external link are left alone — the card uses that link.
     * Uses the query builder (no model) to touch only the slug column, matching
     * CsrProgram::saving()'s derivation.
     */
    public function up(): void
    {
        $rows = DB::table('csr_programs')
            ->where(fn ($q) => $q->whereNull('slug')->orWhere('slug', ''))
            ->where(fn ($q) => $q->whereNull('link')->orWhere('link', ''))
            ->get(['id', 'title_id', 'title_en']);

        foreach ($rows as $row) {
            $title = $row->title_id ?: $row->title_en;
            if (filled($title)) {
                DB::table('csr_programs')->where('id', $row->id)->update([
                    'slug' => Str::slug($title),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Auto-generated slugs are not tracked, so there is nothing to reverse.
    }
};
