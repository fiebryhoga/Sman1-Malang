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
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin Management';
    // ✅ PERBAIKAN: Ubah label
    protected static ?string $label = 'Guru / Pegawai';
    protected static ?string $pluralLabel = 'Daftar Guru / Pegawai';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Guru / Pegawai')
                    ->schema([
                        FileUpload::make('avatar_url')->label('Foto Profil')->image()->avatar()->imageEditor()->circleCropper()->directory('avatars'),
                        // ✅ PERBAIKAN: Ganti email dengan NIP dan tambahkan kolom baru
                        TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->required()
                            ->label('Nama Lengkap'),
                        TextInput::make('no_telepon')
                            ->label('No. Telepon (Opsional)')
                            ->tel(),
                        TextInput::make('email')
                            ->label('Email (Opsional)')
                            ->email()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->columnSpanFull(),
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
                // ✅ PERBAIKAN: Tampilkan NIP, bukan email
                TextColumn::make('nip')->label('NIP')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
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