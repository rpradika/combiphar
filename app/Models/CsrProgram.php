<?php

namespace App\Models;

use App\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CsrProgram extends Model
{
    use HasLocalizedContent;

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
    ];

    protected static function booted(): void
    {
        // A card links to its /csr/{slug} detail page only when it has a slug.
        // Auto-derive one from the title when the admin left both the slug and
        // the external link blank, so the "Selengkapnya" button is never dead
        // (a program with an external link keeps that link — no slug needed).
        static::saving(function (self $program): void {
            $title = $program->title_id ?: $program->title_en;
            if (blank($program->slug) && blank($program->link) && filled($title)) {
                $program->slug = Str::slug($title);
            }
        });
    }

    /** Parent program (null = top-level card). */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CsrProgram::class, 'parent_id');
    }

    /** Sub-topics shown on the detail page. */
    public function children(): HasMany
    {
        return $this->hasMany(CsrProgram::class, 'parent_id')->orderBy('sort');
    }
}