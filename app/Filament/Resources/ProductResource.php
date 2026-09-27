<?php

namespace App\Filament\Resources;

use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Badge;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string { return __('admin.resources.products'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.catalog'); }
    public static function getModelLabel(): string { return __('admin.resources.product'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.products'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                Forms\Components\Select::make('brand_id')
                    ->label(__('admin.resources.brand'))
                    ->options(fn() => Brand::with(['translations' => fn($q) => $q->where('language_id', \App\Models\Language::currentId())])
                        ->where('store_id', 1)->get()
                        ->mapWithKeys(fn($b) => [$b->id => $b->translations->first()?->name ?? "Brand #{$b->id}"]))
                    ->nullable(),
                Forms\Components\Select::make('stock_status_id')
                    ->label(__('admin.resources.stock_status'))
                    ->options(fn() => StockStatus::with(['translations' => fn($q) => $q->where('language_id', \App\Models\Language::currentId())])
                        ->where('store_id', 1)->get()
                        ->mapWithKeys(fn($s) => [$s->id => $s->translations->first()?->name ?? "Status #{$s->id}"]))
                    ->nullable(),
                Forms\Components\TextInput::make('price')
                    ->label('Price / Ціна')
                    ->numeric()
                    ->prefix('₴')
                    ->required(),
                Forms\Components\TextInput::make('old_price')
                    ->label('Old price / Стара ціна')
                    ->numeric()
                    ->prefix('₴')
                    ->nullable(),
                Forms\Components\TextInput::make('preorder_days')
                    ->label(__('admin.fields.preorder_days'))
                    ->helperText(__('admin.hints.preorder_days'))
                    ->numeric()
                    ->minValue(1)
                    ->nullable(),
                Forms\Components\Select::make('replaced_by_id')
                    ->label(__('admin.fields.replaced_by'))
                    ->helperText(__('admin.hints.replaced_by'))
                    ->options(fn(?Product $record) => Product::with(['translations' => fn($q) => $q->where('language_id', \App\Models\Language::currentId())])
                        ->where('store_id', 1)
                        ->when($record, fn($q) => $q->whereKeyNot($record->id))
                        ->orderBy('sku')->get()
                        ->mapWithKeys(fn($p) => [$p->id => $p->sku . ' — ' . ($p->translations->first()?->name ?? '')]))
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make(__('admin.resources.categories') . ' & ' . __('admin.resources.badges'))->schema([
                Forms\Components\Select::make('categories')
                    ->label(__('admin.resources.categories'))
                    ->multiple()
                    ->relationship('categories', 'id')
                    ->options(fn() => Category::with(['translations' => fn($q) => $q->where('language_id', \App\Models\Language::currentId())])
                        ->where('store_id', 1)->get()
                        ->mapWithKeys(fn($c) => [$c->id => $c->translations->first()?->name ?? "Category #{$c->id}"]))
                    ->preload(),
                Forms\Components\Select::make('badges')
                    ->label(__('admin.resources.badges'))
                    ->multiple()
                    ->relationship('badges', 'id')
                    ->options(fn() => Badge::with(['translations' => fn($q) => $q->where('language_id', \App\Models\Language::currentId())])
                        ->where('store_id', 1)->get()
                        ->mapWithKeys(fn($b) => [$b->id => $b->translations->first()?->name ?? "Badge #{$b->id}"]))
                    ->preload(),
            ])->columns(2),

            Forms\Components\Tabs::make(__('admin.sections.translations'))
                ->tabs(
                    \App\Models\Language::where('store_id', 1)->where('is_active', true)->orderBy('id')->get()
                        ->map(fn($lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                            Forms\Components\TextInput::make($lang->code . '_name')
                                ->label(__('admin.fields.name') . ' (' . $lang->code . ')')
                                ->required($lang->is_default)
                                ->maxLength(255),
                            Forms\Components\TextInput::make($lang->code . '_slug')
                                ->label(__('admin.fields.slug') . ' (' . $lang->code . ')')
                                ->required($lang->is_default)
                                ->maxLength(255),
                            Forms\Components\Textarea::make($lang->code . '_description')
                                ->label(__('admin.fields.description') . ' (' . $lang->code . ')')
                                ->rows(4),
                            Forms\Components\Textarea::make($lang->code . '_warning_text')
                                ->label(__('admin.fields.warning_text') . ' (' . $lang->code . ')')
                                ->helperText(__('admin.hints.warning_text'))
                                ->rows(2),
                        ]))->toArray()
                )
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn($record) => $record->translations->where('language_id', \App\Models\Language::currentId())->first()?->name ?? '—'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label(__('admin.resources.brand'))
                    ->getStateUsing(fn($record) => $record->brand?->translations->where('language_id', \App\Models\Language::currentId())->first()?->name ?? '—'),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('UAH')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('admin.fields.is_active'))->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    TranslateAction::makeBulk(['name', 'slug', 'description', 'warning_text']),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['translations', 'brand.translations'])
            ->where('store_id', 1);
    }
}
