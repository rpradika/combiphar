<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Widgets\ChartWidget;

/**
 * Contact-form volume over the trailing 12 months. Months with no messages are
 * filled with 0 so the axis stays continuous rather than skipping gaps.
 */
class ContactMessagesChart extends ChartWidget
{
    protected static ?string $heading = 'Pesan Masuk (12 Bulan Terakhir)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $start = now()->startOfMonth()->subMonths(11);

        // DATE_FORMAT is MySQL-specific; this project is MySQL 8 only.
        $counts = ContactMessage::query()
            ->where('created_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $data = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $data[] = (int) ($counts[$month->format('Y-m')] ?? 0);
        }

        return [
            'datasets' => [[
                'label' => 'Pesan',
                'data' => $data,
                'backgroundColor' => '#DF0077',
                'borderRadius' => 4,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            // Counts are whole numbers — stop Chart.js inventing 0.5 gridlines.
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
