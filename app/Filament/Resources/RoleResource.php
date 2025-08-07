<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?string $label = 'Role';
        protected static ?string $pluralLabel = 'Hak Akses';

    
    

    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_peran');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_peran');
    }

    public static function canEdit(Model $record): bool
    {
        // Menggunakan nama permission Bahasa Indonesia
        return auth()->user()->can('mengelola_peran');
    }

    public static function canDelete(Model $record): bool
    {
        // Menggunakan nama permission Bahasa Indonesia
        return auth()->user()->can('mengelola_peran');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->label('Nama Peran')->required()->unique(ignoreRecord: true),
                ]),
                Section::make('Hak Akses (Permissions)')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship(name: 'permissions', titleAttribute: 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => ucwords(str_replace('_', ' ', $record->name)))
                            ->columns(2)
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Peran')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Dibuat Pada')->dateTime('d-M-Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}