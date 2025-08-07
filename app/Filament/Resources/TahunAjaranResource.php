<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunAjaranResource\Pages;
use App\Models\TahunAjaran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = TahunAjaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $label = 'Tahun Ajaran';
    protected static ?string $pluralLabel = 'Tahun Ajaran';


    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_tahun_ajaran');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_tahun_ajaran');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('mengelola_tahun_ajaran');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('mengelola_tahun_ajaran');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('nama')->label('Nama Tahun Ajaran')->required(),
                    DatePicker::make('tanggal_mulai')->required(),
                    DatePicker::make('tanggal_selesai')->required(),
                    Toggle::make('is_active')->label('Aktifkan Tahun Ajaran ini?'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('tanggal_mulai')->date('d M Y'),
                TextColumn::make('tanggal_selesai')->date('d M Y'),
                IconColumn::make('is_active')->label('Status Aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTahunAjarans::route('/'),
            'create' => Pages\CreateTahunAjaran::route('/create'),
            'edit' => Pages\EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}
