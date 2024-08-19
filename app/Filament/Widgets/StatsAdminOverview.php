<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $color = User::query()->count() >= 10 ? 'warning' : 'success';
        return [
            Stat::make('Users', User::query()->count())
                ->description('All sytem users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($color),
            Stat::make('Teams', Team::query()->count())
              ->description('All teams from the database ')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('success'),
            Stat::make('Employees', Employee::query()->count())
            ->description('All Employess from the database ')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Department', Department::query()->count())
            ->description('All Department from the database ')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
