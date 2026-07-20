<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('user_id')
                //     ->relationship('user', 'name')
                //     ->required(),
                Forms\Components\Section::make('Dstos del Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true,
                                // como el record es Employee, hay que ignorar por user_id
                                modifyRuleUsing: fn(Forms\Get $get, $record, $rule) =>
                                $record ? $rule->ignore($record->user_id, 'id') : $rule
                            ),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->hiddenOn('edit')
                            ->required(fn(string $context) => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->maxLength(255),
                        Forms\Components\Select::make('roles')
                            ->label('Rol')
                            ->multiple()
                            ->options(Role::pluck('name', 'name'))
                            ->preload()
                            ->required(),

                    ])->columns(2),

                Forms\Components\Section::make('Datos Empleado')
                    ->schema([
                        Forms\Components\Select::make('type_document')
                            ->options([
                                'DNI' => 'DNI',
                                'CI' => 'CI',
                                'PASSPORT' => 'Pasaporte',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('number_document')
                            ->maxLength(20)
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->default(null),
                        Forms\Components\DatePicker::make('birthdate'),
                        Forms\Components\TextInput::make('position')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\DatePicker::make('date_hiring'),
                        Forms\Components\TextInput::make('salary')
                            ->numeric()
                            ->prefix('$')
                            ->default(null),
                        Forms\Components\Toggle::make('active')
                            ->default(true),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Nombre completo')
                    ->searchable(['name', 'last_name']),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Cargo'),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('salary')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
