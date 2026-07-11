<?php

namespace App\Filament\Resources\AboutHistoryResource\Pages;

use App\Filament\Resources\AboutHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutHistory extends EditRecord
{
    protected static string $resource = AboutHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
