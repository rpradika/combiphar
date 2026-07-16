<?php

namespace App\Filament\Resources\LegalPageResource\Pages;

use App\Filament\Resources\LegalPageResource;
use Filament\Resources\Pages\ManageRecords;

class ManageLegalPages extends ManageRecords
{
    protected static string $resource = LegalPageResource::class;

    /** Fixed set (terms + privacy) — no create button. */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
