<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisciplinaryPointRecordResource\Pages;
use App\Models\DisciplinaryPointRecord;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\DisciplinaryPointCategory;
use App\Models\Siswa;

class DisciplinaryPointRecordResource extends Resource
{
    protected static ?string $model = DisciplinaryPointRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $label = 'Catatan Poin';
    protected static ?string $navigationGroup = 'Kedisiplinan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    ->required()
                    ->preload(),
                Select::make('disciplinary_point_category_id')
                    ->label('Kategori Pelanggaran')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->label('Nama Pelanggaran')->required()->unique(),
                        TextInput::make('points')->label('Jumlah Poin')->required()->numeric()->minValue(1),
                    ]),
                FileUpload::make('photo')->image()->directory('bukti-pelanggaran')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama_lengkap')->sortable()->searchable()->label('Siswa'),
                TextColumn::make('category.name')->sortable()->searchable()->label('Pelanggaran'),
                TextColumn::make('category.points')->sortable()->label('Poin'),
                ImageColumn::make('photo')->label('Bukti'),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Waktu Laporan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisciplinaryPointRecords::route('/'),
            'create' => Pages\CreateDisciplinaryPointRecord::route('/create'),
            'edit' => Pages\EditDisciplinaryPointRecord::route('/{record}/edit'),
        ];
    }
}