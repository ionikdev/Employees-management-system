<?php

namespace App\Filament\App\Widgets;

use App\Models\Department;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DepartmentAppChart extends ChartWidget
{
    protected static ?string $heading = 'Department chat';
    protected static string $color = 'success';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Trend::query(Department::query()->whereBelongsTo(Filament::getTenant()))
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Employees',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
}

    protected function getType(): string
    {
        return 'line';
    }
}
