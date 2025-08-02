<?php
    
    namespace App\Filament\Resources;
    
    use App\Filament\Resources\MataPelajaranResource\Pages;
    use App\Filament\Resources\MataPelajaranResource\RelationManagers\KelasRelationManager;
    use App\Models\MataPelajaran;
    use Filament\Forms\Components\TextInput;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    
    class MataPelajaranResource extends Resource
    {
        protected static ?string $model = MataPelajaran::class;
    
        protected static ?string $navigationIcon = 'heroicon-o-book-open';
        protected static ?string $navigationGroup = 'Data Akademik';
    
        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    TextInput::make('nama')->required()->unique(ignoreRecord: true),
                ]);
        }
    
        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('nama')->searchable(),
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                ]);
        }
    
        public static function getRelations(): array
        {
            return [
                KelasRelationManager::class,
            ];
        }
    
        public static function getPages(): array
        {
            return [
                'index' => Pages\ListMataPelajarans::route('/'),
                'create' => Pages\CreateMataPelajaran::route('/create'),
                'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
            ];
        }
    }
    