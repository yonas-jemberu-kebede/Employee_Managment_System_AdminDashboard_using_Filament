<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Forms\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Get;
use Filament\Notifications\Notification;
use App\Models\City;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Employee Managment';
    protected static ?string $recordTitleAttribute = 'first_name';
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name','last_name','country.name','department.name','state.name','city.name'];
    }
    public static function getNavigationBadge(): ?string
    {

        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

              ComponentsSection::make('User Name')->schema([
                    Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('middle_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                ])->columns(3),
                ComponentsSection::make('User Address')->schema([
                    Forms\Components\TextInput::make('zip_code')
                    ->required(),
                Forms\Components\DatePicker::make('date_of_birth')
                   ->native(false)
                   ->displayFormat('d/m/Y')
                    ->required(),
                Forms\Components\DatePicker::make('date_hired')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->maxDate(now())
                    ->required(),

                ])->columns(3),
                ComponentsSection::make('Relationships')->schema([
                        
                    Forms\Components\Select::make('country_id')
                    ->relationship('country','name')
                    ->searchable()
                    ->preload()
                    ->reactive()
                        ->required(),
                    Forms\Components\Select::make('state_id')
                        ->options(function(callable $get){
                            $countryId=$get('country_id');
                            if($countryId){
                                return State::where('country_id',$get('country_id'))->pluck('name','id');
                            }
                                return [];

                            })
                     
                        ->searchable()
                        ->preload()
                        ->reactive()
                     ->required(),

                     Forms\Components\Select::make('city_id')
                     ->options(function(callable $get){
                        $stateId=$get('state_id');
                        if($stateId){
                            return City::where('state_id',$stateId)->pluck('name','id');
                        }
                        return [];
                     })
                     ->searchable()
                     ->preload()
                         ->required(),

               
                    Forms\Components\Select::make('department_id')
                    ->relationship('department','name')
                    ->searchable()
                    ->preload()
                        ->required(),
                   
    
                  
                   
                 
                   
                ])->columns(2),



                   
          
             
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('department.name')
                ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('middle_name')
                ->toggleable()
                ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                ->toggleable()
                ->sortable() ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Department')
                 ->relationship('department','name')
                 ->searchable()
                 ->preload()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                 ->SuccessNotification(
                    Notification::make()
                      ->success()
                      ->title('Employee deleted successfully!')
                      ->body('congrats!')
                 ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
