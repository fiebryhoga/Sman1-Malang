<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelanggaranRecordResource\Pages;
use App\Models\Pelanggaran;
use App\Models\PelanggaranRecord;
use App\Models\Siswa;
use Filament\Forms\Components\DatePicker; 
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PelanggaranRecordResource extends Resource
{
    protected static ?string $model = PelanggaranRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $label = 'Catatan Pelanggaran';
    protected static ?string $pluralLabel = 'Catatan Pelanggaran';

    public static function canViewAny(): bool { return auth()->user()->can('mengelola_pelanggaran'); }
    public static function canCreate(): bool { return auth()->user()->can('mengelola_pelanggaran'); }
    public static function canEdit(Model $record): bool { return auth()->user()->can('mengelola_pelanggaran'); }
    public static function canDelete(Model $record): bool { return auth()->user()->can('mengelola_pelanggaran'); }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Input Pelanggaran Siswa')->schema([
                    Select::make('siswa_id')
                        ->label('Siswa')
                        ->options(
                            Siswa::with('kelas')->get()->mapWithKeys(function ($siswa) {
                                $kelasNama = $siswa->kelas->nama ?? 'Tanpa Kelas';
                                $label = "{$siswa->nis} - {$siswa->nama_lengkap} - {$kelasNama}";
                                return [$siswa->id => $label];
                            })
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('pelanggaran_id')
                        ->label('Jenis Pelanggaran')
                        ->options(
                            Pelanggaran::all()->groupBy('grup')->map(function ($grup) {
                                return $grup->pluck('deskripsi', 'id');
                            })->toArray()
                        )
                        ->searchable()
                        ->required(),
                    Textarea::make('catatan')
                        ->nullable()
                        ->columnSpanFull(),
                    FileUpload::make('photo')
                        ->label('Foto Bukti (Opsional)')
                        ->image()
                        ->directory('bukti-pelanggaran')
                        ->imageEditor(),
                ])->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nis')->label('NIS')->searchable()->sortable(),
                TextColumn::make('siswa.nama_lengkap')->label('Nama Siswa')->searchable()->sortable(),
                TextColumn::make('siswa.kelas.nama')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('pelanggaran.deskripsi')->label('Pelanggaran')->searchable()->limit(50),
            ])
            ->filters([
                // ✅ FILTER KELAS
                Tables\Filters\SelectFilter::make('kelas')
                    ->label('Filter Kelas')
                    ->relationship('siswa.kelas', 'nama')
                    ->searchable()
                    ->preload(),
                
                // ✅ FILTER PELANGGARAN
                Tables\Filters\SelectFilter::make('pelanggaran_id')
                    ->label('Filter Pelanggaran')
                    ->relationship('pelanggaran', 'deskripsi')
                    ->searchable()
                    ->preload(),

                // ✅ FILTER TANGGAL
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Dicatat Dari Tanggal'),
                        DatePicker::make('created_until')->label('Dicatat Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // ... (method infolist, getEloquentQuery, getRelations, dan getPages() tidak berubah) ...
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfolistSection::make('Detail Pelanggaran')
                ->schema([
                    TextEntry::make('siswa.nama_lengkap'),
                    TextEntry::make('siswa.nis')->label('NIS'),
                    TextEntry::make('pelanggaran.kode')->label('Kode Pelanggaran'),
                    TextEntry::make('pelanggaran.deskripsi')->label('Deskripsi Pelanggaran'),
                    TextEntry::make('catatan')->columnSpanFull(),
                    ImageEntry::make('photo')->label('Foto Bukti')->width(200)->disk('public'),
                    TextEntry::make('user.name')->label('Dicatat Oleh'),
                    TextEntry::make('created_at')->label('Waktu Dicatat')->dateTime('l, d F Y H:i'),
                ])->columns(2),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['siswa.kelas', 'pelanggaran', 'user']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggaranRecords::route('/'),
            'create' => Pages\CreatePelanggaranRecord::route('/create'),
            'view' => Pages\ViewPelanggaranRecord::route('/{record}'),
            'edit' => Pages\EditPelanggaranRecord::route('/{record}/edit'),
        ];
    }
}