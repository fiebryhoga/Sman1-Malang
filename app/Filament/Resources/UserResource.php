<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?string $label = 'Pengguna';
        protected static ?string $pluralLabel = 'Daftar User';


    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_pengguna');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_pengguna');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('mengelola_pengguna');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('mengelola_pengguna');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pengguna')
                    ->schema([
                        FileUpload::make('avatar_url')->label('Foto Profil')->image()->avatar()->imageEditor()->circleCropper()->directory('avatars'),
                        TextInput::make('name')->required()->label('Nama Lengkap'),
                        TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        TextInput::make('password')->password()->dehydrateStateUsing(fn ($state) => Hash::make($state))->dehydrated(fn ($state) => filled($state))->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),
                Section::make('Peran (Role)')
                    ->schema([
                        Select::make('roles')->relationship('roles', 'name')->label('Pilih Peran')->multiple()->preload()->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')->label('Foto')->circular(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('roles.name')->label('Peran')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}