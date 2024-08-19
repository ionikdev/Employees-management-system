<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Filament\App\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Employee Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Relationships')

                ->schema([
                    Forms\Components\Select::make('country_id')
                    ->relationship(name: 'country', titleAttribute: 'name' )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (Set $set){
                         $set('state_id', null);
                         $set('city_id', null);
                    })

                    ->required(),


                    Forms\Components\Select::make('state_id')
                    ->options(fn(Get $get): Collection => State::query()
                            ->where('country_id', $get('country_id'))
                            ->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Set $set)=> $set('city_id', null))
                    ->native(false)
                    ->required(),


                    Forms\Components\Select::make('city_id')
                    ->options(fn(Get $get): Collection => City::query()
                    ->where('state_id', $get('state_id'))
                    ->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->native(false)
                    ->required(),


                    Forms\Components\Select::make('department_id')
                    ->relationship(
                        name: 'department', titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())

                         )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                ])->columns(2),

    Forms\Components\Section::make('User Name')
    ->description('Put the user name details in.')
    ->schema([
        Forms\Components\TextInput::make('first_name')
        ->required()
        ->maxLength(255),
    Forms\Components\TextInput::make('last_name')
        ->required()
        ->maxLength(255),
    Forms\Components\TextInput::make('middle_name')
        ->required()
        ->maxLength(255),
    ])->columns(3),
    Forms\Components\Section::make('User Address')
    ->schema([
        Forms\Components\TextInput::make('address')
        ->required()
        ->maxLength(255),
        Forms\Components\TextInput::make('zip_code')
        ->required()
        ->maxLength(255),
    ])->columns(2),

    Forms\Components\Section::make('Dates')
    ->schema([
        Forms\Components\DatePicker::make('date_of_birth')
        ->native(false)
        ->displayFormat('d/m/y')
        ->required(),
    Forms\Components\DatePicker::make('date_of_hired')
    ->native(false)
    ->displayFormat('d/m/y')
        ->required(),
    ])->columns(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('first_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('last_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('middle_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('address')
                ->searchable(),
            Tables\Columns\TextColumn::make('zip_code')
                ->searchable(),
            Tables\Columns\TextColumn::make('date_of_birth')
            ->toggleable(isToggledHiddenByDefault: true)
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('date_of_hired')
            ->toggleable(isToggledHiddenByDefault: true)
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
                 ->filters([
                SelectFilter::make('Department')
                ->relationship('department', 'name')
                ->label('Filter By Department')
                ->preload()
                ->indicator('Department'),
                SelectFilter::make('address')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ]),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist

    {
        return $infolist
        ->schema([
            Section::make('Employee Info')
            ->schema([
                Section::make('User Information')
                ->schema([
                    TextEntry::make('first_name')->label('First Name'),
                    TextEntry::make('last_name')->label('Last Name'),
                    TextEntry::make('middle_name')->label('Middle Name'),

            ])->columns(3),
                Section::make('Address')
                ->schema([
                    TextEntry::make('address')->label('Address'),
                    TextEntry::make('zip_code')->label('Zip Code'),

            ])->columns(3),
                Section::make('Dates')
                ->schema([

                    TextEntry::make('date_of_birth')->label('Date of Birth'),
                    TextEntry::make('date_of_hired')->label('Date of Hired'),

            ])->columns(3),

                Section::make('Relationship')
                ->schema([

                    TextEntry::make('department.name')->label('Department'),
                    TextEntry::make('country.name')->label('Country'),
                    TextEntry::make('state.name')->label('State'),
                    TextEntry::make('city.name')->label('City'),

            ])->columns(3)




        ])->columns(3)
        ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
