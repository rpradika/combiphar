<?php

namespace App\Filament\Resources\AboutHistoryResource\Pages;

use App\Filament\Resources\AboutHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAboutHistories extends ListRecords
{
    protected static string $resource = AboutHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
