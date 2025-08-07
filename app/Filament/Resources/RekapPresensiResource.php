<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekapPresensiResource\Pages;
use App\Models\Presensi;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;

class RekapPresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $label = 'Rekap Presensi';
    protected static bool $shouldRegisterNavigation = true;

    public static function canCreate(): bool { return false; }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('mengelola_presensi_semua');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('mengelola_presensi_semua');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->can('melihat_presensi_semua');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_presensi_semua');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['kelas', 'mataPelajaran', 'guru'])
            ->withCount([
                'detailPresensi as hadir_count' => fn (Builder $query) => $query->where('status', 'hadir'),
                'detailPresensi as sakit_count' => fn (Builder $query) => $query->where('status', 'sakit'),
                'detailPresensi as izin_count' => fn (Builder $query) => $query->where('status', 'izin'),
                'detailPresensi as alpha_count' => fn (Builder $query) => $query->where('status', 'alpha'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kelas.nama')->label('Kelas')->sortable(),
                TextColumn::make('mataPelajaran.nama')->label('Mata Pelajaran')->sortable(),
                TextColumn::make('tanggal')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('guru.name')->label('Guru Pengampu')->sortable(),
                TextColumn::make('hadir_count')->label('Hadir'),
                TextColumn::make('sakit_count')->label('Sakit'),
                TextColumn::make('izin_count')->label('Izin'),
                TextColumn::make('alpha_count')->label('Alpha'),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('bulan')
                    ->options([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['value'] ?? null, fn (Builder $query, $bulan) => $query->whereMonth('tanggal', $bulan))
                    ),
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('tanggal_mulai')->label('Dari Tanggal'),
                        DatePicker::make('tanggal_selesai')->label('Sampai Tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['tanggal_mulai'] ?? null, fn (Builder $query, $date) => $query->whereDate('tanggal', '>=', $date))
                        ->when($data['tanggal_selesai'] ?? null, fn (Builder $query, $date) => $query->whereDate('tanggal', '<=', $date))
                    ),
            ])
            ->actions([
                ViewAction::make()->visible(fn (Model $record): bool => auth()->user()->can('melihat_presensi_semua')),
                EditAction::make()->visible(fn (Model $record): bool => auth()->user()->can('mengelola_presensi_semua')),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekapPresensis::route('/'),
            'view' => Pages\ViewRekapPresensis::route('/{record}'),
        ];
    }
}