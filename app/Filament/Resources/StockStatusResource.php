<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\StockStatusResource\Pages;
use App\Models\StockStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockStatusResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager', 'content'];

    protected static ?string $model = StockStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string { return __('admin.resources.stock_statuses'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.catalog'); }
    public static function getModelLabel(): string { return __('admin.resources.stock_status'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.stock_statuses'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('admin.fields.code'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('color')
                    ->label(__('admin.fields.color'))
                    ->required()
                    ->maxLength(20)
                    ->default('#ffffff'),
                Forms\Components\TextInput::make('bg_color')
                    ->label(__('admin.fields.bg_color'))
                    ->required()
                    ->maxLength(20)
                    ->default('#000000'),
                Forms\Components\TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->required(),
            ])->columns(2),

            Forms\Components\Tabs::make(__('admin.sections.translations'))
                ->tabs(
                    \App\Models\Language::query()->where('is_active', true)->orderBy('id')->get()
                        ->map(fn($lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                            Forms\Components\TextInput::make($lang->code . '_name')
                                ->label(__('admin.fields.name') . ' (' . $lang->code . ')')
                                ->required($lang->is_default)
                                ->maxLength(255),
                        ]))->toArray()
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('admin.fields.code'))->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn($record) => $record->translations->first()?->name ?? '—'),
                Tables\Columns\ColorColumn::make('bg_color')->label(__('admin.fields.bg_color')),
                Tables\Columns\TextColumn::make('sort_order')->label(__('admin.fields.sort_order'))->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('admin.fields.is_active'))->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    TranslateAction::makeBulk(['name']),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStockStatuses::route('/'),
            'create' => Pages\CreateStockStatus::route('/create'),
            'edit'   => Pages\EditStockStatus::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
