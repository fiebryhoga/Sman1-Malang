<?php
namespace App\Filament\Resources;

use App\Filament\Resources\EkstrakurikulerResource\Pages;
use App\Filament\Resources\EkstrakurikulerResource\RelationManagers;
use App\Models\Ekstrakurikuler;
use Filament\Forms\Components\Section; // ✅ 1. Tambahkan ini
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction; // ✅ 2. Tambahkan ini
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EkstrakurikulerResource extends Resource
{
    protected static ?string $model = Ekstrakurikuler::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $label = 'Ekstrakurikuler';

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('Super Admin')) {
            return parent::getEloquentQuery()->withCount('siswas');
        }
        return parent::getEloquentQuery()
            ->whereHas('pembina', fn(Builder $query) => $query->where('user_id', auth()->id()))
            ->withCount('siswas');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    // ✅ 3. Tambahkan izin untuk menghapus, hanya untuk Super Admin
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ✅ 4. Bungkus form utama dalam Section yang hanya terlihat oleh Admin
                Section::make('Detail Ekstrakurikuler')
                    ->schema([
                        TextInput::make('nama')->required()->unique(ignoreRecord: true),
                        Textarea::make('deskripsi')->columnSpanFull(),
                        Select::make('pembina')
                            ->label('Guru Pembina')
                            ->relationship('pembina', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                    ->visible(fn (): bool => auth()->user()->hasRole('Super Admin'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('pembina.name')->label('Guru Pembina')->listWithLineBreaks()->limitList(2),
                TextColumn::make('siswas_count')->label('Jumlah Anggota'),
            ])
            ->actions([
                EditAction::make()
                ->label('Kelola Ekstrakurikuler'),
                DeleteAction::make(), // Tombol ini akan otomatis tersembunyi untuk Guru
            ])
            ->recordUrl(
                fn (Model $record): string => static::getUrl('edit', ['record' => $record])
            );
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\SiswaRelationManager::class,
            RelationManagers\PresensiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEkstrakurikulers::route('/'),
            'create' => Pages\CreateEkstrakurikuler::route('/create'),
            'edit' => Pages\EditEkstrakurikuler::route('/{record}/edit'),
            
        ];
    }    
}



//             'view' => Pages\ViewEkstrakurikuler::route('/{record}'),
