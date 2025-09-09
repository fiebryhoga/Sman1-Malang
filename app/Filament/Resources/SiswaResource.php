<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Models\Siswa;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SiswaResource extends Resource
{
    protected static ?string $model = \App\Models\Siswa::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $label = 'Siswa';
    protected static ?string $pluralLabel = 'Daftar Siswa';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function canViewAny(): bool { return auth()->user()->can('melihat_siswa'); }
    public static function canCreate(): bool { return auth()->user()->can('mengelola_siswa'); }
    public static function canEdit(Model $record): bool { return auth()->user()->can('mengelola_siswa'); }
    public static function canDelete(Model $record): bool { return auth()->user()->can('mengelola_siswa'); }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['kelas']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pribadi Siswa')->schema([
                    FileUpload::make('foto')->image()->avatar()->imageEditor()->circleCropper()->directory('foto-siswa')->columnSpanFull(),
                    TextInput::make('nis')->label('Nomor Induk Siswa (NIS)')->required()->unique(ignoreRecord: true),
                    TextInput::make('nisn')
                        ->label('Nomor Induk Siswa Nasional (NISN)')
                        ->nullable(),
                        // ✅ PERBAIKAN: Aturan ->unique(ignoreRecord: true) dihapus dari sini
                    TextInput::make('nama_lengkap')->required()->columnSpanFull(),
                    Radio::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])->nullable(),
                    Select::make('kelas_id')->relationship('kelas', 'nama')->searchable()->preload()->label('Kelas'),
                    Select::make('agama')->options(['Islam'=>'Islam', 'Kristen'=>'Kristen', 'Katolik'=>'Katolik', 'Hindu'=>'Hindu', 'Buddha'=>'Buddha', 'Konghucu'=>'Konghucu'])->nullable(),
                    TextInput::make('angkatan')->numeric()->nullable(),
                    TextInput::make('nomor_ortu')->label('Nomor HP Orang Tua')->tel()->nullable(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn (Model $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama_lengkap)),
                TextColumn::make('nis')->searchable()->sortable(),
                TextColumn::make('nama_lengkap')->label('Nama')->searchable(),
                TextColumn::make('kelas.nama')->searchable()->sortable()->default('Belum ada kelas'),
                TextColumn::make('angkatan')->sortable(),
            ])
            ->filters([
                SelectFilter::make('kelas_id')->relationship('kelas', 'nama')->searchable()->preload()->label('Filter Kelas'),
                SelectFilter::make('angkatan')->label('Filter Angkatan')->options(
                    Siswa::query()->distinct()->pluck('angkatan', 'angkatan')->filter()->sort()
                ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('pindahkan_kelas')
                        ->label('Pindahkan Kelas')
                        ->icon('heroicon-o-building-library')
                        ->form([
                            Select::make('kelas_id_baru')
                                ->label('Pilih Kelas Baru')
                                ->relationship('kelas', 'nama')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update([
                                'kelas_id' => $data['kelas_id_baru'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
            'view' => Pages\ViewSiswa::route('/{record}'),
        ];
    }
}