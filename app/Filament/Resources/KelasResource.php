<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers\SiswasRelationManager;
use App\Models\Kelas;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KelasResource extends Resource
{
    protected static ?string $model = \App\Models\Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $label = 'Kelas';
    protected static ?string $pluralLabel = 'Daftar Kelas';

    /**
     * ✅ TAMBAHKAN INI: Menambahkan badge (lencana) dengan jumlah total kelas.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * (Opsional) Memberi warna pada badge.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
    
    // ... (method-method can... tidak berubah) ...
    public static function canViewAny(): bool { return auth()->user()->can('melihat_kelas'); }
    public static function canView(Model $record): bool { return auth()->user()->can('melihat_kelas'); }
    public static function canCreate(): bool { return auth()->user()->can('mengelola_kelas'); }
    public static function canEdit(Model $record): bool { return auth()->user()->can('mengelola_kelas'); }
    public static function canDelete(Model $record): bool { return auth()->user()->can('mengelola_kelas'); }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('tahun_ajaran_id')
                        ->relationship('tahunAjaran', 'nama')
                        ->searchable()->preload()->required(),
                    TextInput::make('nama')
                        ->label('Nama Kelas')->required(),
                    Select::make('wali_kelas_id')
                        ->label('Wali Kelas')
                        ->options(User::role('Guru Wali Kelas')->pluck('name', 'id'))
                        ->searchable()->preload(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('waliKelas.name')->label('Wali Kelas')->default('Belum diatur'),
                TextColumn::make('tahunAjaran.nama')->label('Tahun Ajaran'),
                TextColumn::make('siswas_count')->counts('siswas')->label('Jumlah Siswa'),
            ])
            ->filters([
                Filter::make('kelas_diampu')
                    ->label('Tampilkan Kelas Saya Saja')
                    ->query(function (Builder $query): Builder {
                        $userId = auth()->id();
                        $kelasDiajarIds = DB::table('kelas_mata_pelajaran')
                            ->where('user_id', $userId)
                            ->pluck('kelas_id')
                            ->unique();
                        return $query->whereIn('id', $kelasDiajarIds)
                                     ->orWhere('wali_kelas_id', $userId);
                    })
                    ->toggle()
                    ->visible(fn (): bool => auth()->user()->hasRole(['Guru Wali Kelas', 'Guru Mata Pelajaran'])),

                SelectFilter::make('tingkat')
                    ->options([
                        'X' => 'Kelas X',
                        'XI' => 'Kelas XI',
                        'XII' => 'Kelas XII',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return $query->where('nama', 'like', $data['value'] . '-%');
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->visible(fn (Model $record): bool => static::canEdit($record)),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SiswasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'view' => Pages\ViewKelas::route('/{record}'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}