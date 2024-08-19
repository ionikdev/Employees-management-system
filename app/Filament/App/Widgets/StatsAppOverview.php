<?php

namespace App\Filament\App\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAppOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $color = User::query()->count() >= 10 ? 'warning' : 'success';
        return [
            Stat::make('Users', Team::find(Filament::getTenant())->first()->members->count())
                ->description('All sytem users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($color),
            Stat::make('Departments', Department::query()->whereBelongsTo(Filament::getTenant())->count())
              ->description('All teams from the database ')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('success'),
                
            Stat::make('Employees', Employee::query()->whereBelongsTo(Filament::getTenant())->count())
            ->description('All Employess from the database ')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

        ];
    }
}
