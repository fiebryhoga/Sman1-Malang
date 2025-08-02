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
use Illuminate\Database\Eloquent\Model;

class SiswaResource extends Resource
{
    protected static ?string $model = \App\Models\Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $label = 'Siswa';

    /**
     * Memperbaiki pengecekan izin untuk melihat resource Siswa.
     * Menggunakan izin 'melihat_siswa' yang sudah didefinisikan di seeder.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_siswa');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_siswa');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('mengelola_siswa');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('mengelola_siswa');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pribadi Siswa')->schema([
                    FileUpload::make('foto')->image()->avatar()->imageEditor()->circleCropper()->directory('foto-siswa'),
                    TextInput::make('nis')->label('Nomor Induk Siswa (NIS)')->required()->unique(ignoreRecord: true),
                    TextInput::make('nama_lengkap')->required(),
                    Radio::make('jenis_kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])->required(),
                    Select::make('kelas_id')->relationship('kelas', 'nama')->searchable()->preload()->label('Kelas'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')->circular(),
                TextColumn::make('nis')->searchable()->sortable(),
                TextColumn::make('nama_lengkap')->searchable(),
                TextColumn::make('kelas.nama')->searchable()->sortable()->default('Belum ada kelas'),
                TextColumn::make('jenis_kelamin'),
            ])
            ->filters([
                // Filter berdasarkan kelas
                SelectFilter::make('kelas_id')
                    ->relationship('kelas', 'nama')
                    ->searchable()
                    ->preload()
                    ->label('Filter Berdasarkan Kelas'),

                // Filter berdasarkan jenis kelamin
                SelectFilter::make('jenis_kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
