<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelanggaranRecordResource\Pages;
use App\Models\Pelanggaran;
use App\Models\PelanggaranRecord;
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

    // ... (method can... tidak berubah) ...
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
                        ->relationship('siswa', 'nama_lengkap')
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
                        ->imageEditor()
                        ->disk('public'),

                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama_lengkap')->searchable()->sortable(),
                TextColumn::make('pelanggaran.kode')->label('Kode')->sortable(),
                TextColumn::make('pelanggaran.deskripsi')->label('Pelanggaran')->searchable()->limit(50),
                TextColumn::make('user.name')->label('Dicatat Oleh')->sortable(),
                
                // ✅ 3. Ubah format tanggal di tabel
                TextColumn::make('created_at')
                    ->label('Tanggal Dicatat')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y H:i'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('siswa_id')
                    ->label('Filter Siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    ->preload(),
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
                    
                    // ✅ 4. Ubah format tanggal di halaman detail
                    TextEntry::make('created_at')
                        ->label('Waktu Dicatat')
                        ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y H:i')),
                ])->columns(2),
        ]);
    }
    
    // ... (sisa file tidak berubah) ...
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['siswa', 'pelanggaran', 'user']);
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