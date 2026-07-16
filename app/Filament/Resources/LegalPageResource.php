<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalPageResource\Pages;
use App\Models\LegalPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LegalPageResource extends Resource
{
    protected static ?string $model = LegalPage::class;

    protected static ?string $navigationGroup = 'Karir & Kontak';

    protected static ?string $navigationLabel = 'Halaman Legal (Footer)';

    protected static ?int $navigationSort = 90;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Kunci')
                    ->helperText('Identitas halaman (terms / privacy) — jangan diubah.')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('title_id')
                    ->label('Judul (ID)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('title_en')
                    ->label('Title (EN)')
                    ->maxLength(255),
                Forms\Components\RichEditor::make('body_id')
                    ->label('Isi Halaman (ID)')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body_en')
                    ->label('Page Content (EN)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable(),
                Tables\Columns\TextColumn::make('title_id')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLegalPages::route('/'),
        ];
    }
}
