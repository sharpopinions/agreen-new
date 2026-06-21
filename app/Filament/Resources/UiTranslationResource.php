<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UiTranslationResource\Pages;
use App\Models\Language;
use App\Models\UiTranslation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UiTranslationResource extends Resource
{
    protected static ?string $model = UiTranslation::class;
    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationGroup = 'Налаштування';
    protected static ?string $navigationLabel = 'Системні переклади';
    protected static ?string $modelLabel = 'Переклад';
    protected static ?string $pluralModelLabel = 'Системні переклади';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $languages = Language::where('store_id', 1)->where('is_active', true)->orderBy('id')->get();

        return $form->schema([
            Forms\Components\Section::make('Ключ')->schema([
                Forms\Components\Select::make('group')
                    ->label('Розділ')
                    ->options([
                        'admin'    => '⚙️ Адмінка',
                        'frontend' => '🌐 Фронтенд',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('key')
                    ->label('Ключ')
                    ->placeholder('fields.name або cart.add_button')
                    ->required()
                    ->maxLength(255),
            ])->columns(2),

            Forms\Components\Section::make('Переклади')->schema(
                $languages->map(fn($lang) => Forms\Components\TextInput::make('value_' . $lang->code)
                    ->label($lang->name . ' (' . $lang->code . ')')
                    ->required($lang->is_default)
                    ->maxLength(500)
                )->toArray()
            ),
        ]);
    }

    public static function table(Table $table): Table
    {
        $languages = Language::where('store_id', 1)->where('is_active', true)->orderBy('id')->get();

        $columns = [
            Tables\Columns\TextColumn::make('group')
                ->label('Розділ')
                ->badge()
                ->color(fn($state) => match($state) {
                    'admin'    => 'warning',
                    'frontend' => 'info',
                    default    => 'gray',
                })
                ->formatStateUsing(fn($state) => match($state) {
                    'admin'    => 'Адмінка',
                    'frontend' => 'Фронтенд',
                    default    => $state,
                })
                ->sortable(),

            Tables\Columns\TextColumn::make('key')
                ->label('Ключ')
                ->searchable()
                ->sortable()
                ->fontFamily('mono'),
        ];

        foreach ($languages as $lang) {
            $columns[] = Tables\Columns\TextColumn::make('value_' . $lang->code)
                ->label($lang->code)
                ->getStateUsing(fn($record) => $record->values->where('locale', $lang->code)->first()?->value ?? '—')
                ->wrap()
                ->limit(50);
        }

        return $table
            ->columns($columns)
            ->defaultSort('key')
            ->groups([
                Tables\Grouping\Group::make('group')
                    ->label('Розділ')
                    ->getTitleFromRecordUsing(fn($record) => match($record->group) {
                        'admin'    => '⚙️ Адмінка',
                        'frontend' => '🌐 Фронтенд',
                        default    => $record->group,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Розділ')
                    ->options([
                        'admin'    => 'Адмінка',
                        'frontend' => 'Фронтенд',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('values')->where('store_id', 1);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUiTranslations::route('/'),
            'create' => Pages\CreateUiTranslation::route('/create'),
            'edit'   => Pages\EditUiTranslation::route('/{record}/edit'),
        ];
    }
}
