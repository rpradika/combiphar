<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\JobVacancy;
use App\Models\Office;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Dashboard headline numbers. Auto-registered via the panel's
 * discoverWidgets() — no explicit registration needed.
 */
class ContentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $articles = Article::count();
        $published = Article::published()->count();
        $messages = ContactMessage::count();
        $thisMonth = ContactMessage::where('created_at', '>=', now()->startOfMonth())->count();
        $vacancies = JobVacancy::count();
        $open = JobVacancy::where('is_open', true)->count();

        return [
            Stat::make('Produk', Product::count())
                ->description(Office::count().' lokasi kantor & pabrik')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Berita', $articles)
                ->description($published === $articles
                    ? 'Semua sudah terbit'
                    : $published.' terbit, '.($articles - $published).' draf')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),

            Stat::make('Pesan Masuk', $messages)
                ->description($thisMonth > 0 ? $thisMonth.' bulan ini' : 'Belum ada bulan ini')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($thisMonth > 0 ? 'magenta' : 'gray'),

            Stat::make('Lowongan Aktif', $open)
                ->description($vacancies - $open > 0
                    ? ($vacancies - $open).' lowongan ditutup'
                    : 'Semua lowongan terbuka')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color($open > 0 ? 'primary' : 'gray'),
        ];
    }
}
