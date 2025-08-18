<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiHarianResource\Pages;
use App\Filament\Resources\PresensiHarianResource\RelationManagers;
use App\Models\PresensiHarian;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction; // ✅ 1. Tambahkan ini
use Filament\Tables\Actions\DeleteBulkAction; // ✅ 2. Tambahkan ini
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PresensiHarianResource extends Resource
{
    protected static ?string $model = PresensiHarian::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $label = 'Presensi Harian';
    
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('mengelola presensi harian');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('mengelola presensi harian');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('tanggal')
                    ->label('Tanggal Presensi')
                    ->default(now())
                    ->required()
                    ->unique(ignoreRecord: true),
                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->default('07:00')
                    ->required(),
                TimePicker::make('batas_presensi')
                    ->label('Sesi Ditutup Pukul')
                    ->default('23:59')
                    ->required(),
                Select::make('status')
                    ->options([
                        'buka' => 'Buka',
                        'tutup' => 'Tutup',
                    ])
                    ->default('buka')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')->date('l, d F Y')->sortable(),
                TextColumn::make('jam_masuk')->time('H:i'),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'buka' => 'heroicon-o-lock-open',
                        'tutup' => 'heroicon-o-lock-closed',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'buka' => 'success',
                        'tutup' => 'danger',
                    }),
                TextColumn::make('user.name')->label('Dibuat Oleh'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(), // ✅ 3. Tambahkan tombol hapus di sini
            ])
            ->bulkActions([
                DeleteBulkAction::make(), // ✅ 4. Tambahkan aksi hapus massal
            ])
            ->defaultSort('tanggal', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailsRelationManager::class,
        ];
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfolistSection::make('Detail Sesi Presensi Harian')
                ->schema([
                    TextEntry::make('tanggal')->date('l, d F Y'),
                    TextEntry::make('jam_masuk')->time('H:i'),
                    TextEntry::make('status')->badge()->color(fn(string $state) => match ($state) {
                        'buka' => 'success',
                        'tutup' => 'danger',
                    }),
                    TextEntry::make('user.name')->label('Dibuat Oleh'),
                ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresensiHarians::route('/'),
            'create' => Pages\CreatePresensiHarian::route('/create'),
            'edit' => Pages\EditPresensiHarian::route('/{record}/edit'),
            'view' => Pages\ViewPresensiHarian::route('/{record}'),
        ];
    }
}