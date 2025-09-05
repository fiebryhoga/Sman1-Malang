<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelanggaranResource\Pages;
use App\Models\Pelanggaran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model; 

class PelanggaranResource extends Resource
{
    protected static ?string $model = Pelanggaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $label = 'Jenis Pelanggaran';
    protected static ?string $pluralLabel = 'Daftar Jenis Pelanggaran';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('mengelola jenis pelanggaran');
    }
    public static function canCreate(): bool { return auth()->user()->can('mengelola jenis pelanggaran'); }
    public static function canEdit(Model $record): bool { return auth()->user()->can('mengelola jenis pelanggaran'); }
    public static function canDelete(Model $record): bool { return auth()->user()->can('mengelola jenis pelanggaran'); }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('grup')
                    ->label('Grup Pelanggaran')
                    ->options([
                        'A' => 'Grup A (Ringan)',
                        'B' => 'Grup B (Sedang)',
                        'C' => 'Grup C (Berat)',
                    ])
                    ->required(),
                TextInput::make('kode')
                    ->label('Kode Pelanggaran (Contoh: A1, B5)')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                Textarea::make('deskripsi')
                    ->label('Deskripsi Pelanggaran')
                    ->required()
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grup')->sortable()->searchable(),
                TextColumn::make('kode')->sortable()->searchable(),
                TextColumn::make('deskripsi')->searchable()->limit(70),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grup')
                    ->options([
                        'A' => 'Grup A',
                        'B' => 'Grup B',
                        'C' => 'Grup C',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kode');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggarans::route('/'),
            'create' => Pages\CreatePelanggaran::route('/create'),
            'edit' => Pages\EditPelanggaran::route('/{record}/edit'),
        ];
    }    
}