<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataPelajaranResource\Pages;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\User;
use App\Models\JamPelajaran;
use App\Models\JadwalMengajar;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Illuminate\Support\Collection;

class MataPelajaranResource extends Resource
{
    protected static ?string $model = MataPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Data Master';
    
    protected static ?string $label = 'Mata Pelajaran';
    protected static ?string $pluralLabel = 'Mata Pelajaran';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_mata_pelajaran');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_mata_pelajaran');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('mengelola_mata_pelajaran');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('mengelola_mata_pelajaran');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Nama Mata Pelajaran')
                    ->schema([
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                    ]),
                
                Section::make('Jadwal Mengajar')
                    ->schema([
                        Repeater::make('jadwalMengajar')
                            ->label(false)
                            ->relationship('jadwalMengajar')
                            ->schema([
                                Select::make('kelas_id')
                                    ->label('Kelas')
                                    ->options(
                                        fn (Get $get, ?Model $record): Collection =>
                                        Kelas::whereNotIn('id', collect($get('..*.kelas_id'))->filter())->pluck('nama', 'id')
                                    )
                                    ->reactive()
                                    ->required(),
                                Select::make('user_id')
                                    ->label('Guru Pengampu')
                                    ->relationship('guru', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Select::make('jamPelajaran')
                                    ->label('Jam Pelajaran Ke')
                                    ->relationship('jamPelajaran', 'jam_ke')
                                    ->multiple()
                                    ->preload()
                                    ->required(),
                            ])
                            // --- PERBAIKAN DI SINI ---
                            ->itemLabel(function (array $state): ?string {
                                $kelasId = $state['kelas_id'] ?? null;
                                if ($kelasId) {
                                    $kelas = Kelas::find($kelasId);
                                    return $kelas ? 'Jadwal untuk ' . $kelas->nama : null;
                                }
                                return null;
                            })
                            // --- AKHIR PERBAIKAN ---
                            ->minItems(1)
                            ->collapsible()
                            ->defaultItems(1)
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('jadwalMengajar.kelas.nama')->label('Kelas')->badge()->sortable(),
                TextColumn::make('jadwalMengajar.guru.name')->label('Guru Pengampu')->badge()->sortable(),
                TextColumn::make('jadwalMengajar.jamPelajaran.jam_ke')->label('Jam Ke')->badge()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMataPelajarans::route('/'),
            'create' => Pages\CreateMataPelajaran::route('/create'),
            'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
            'view' => Pages\ViewMataPelajaran::route('/{record}'),
        ];
    }
}