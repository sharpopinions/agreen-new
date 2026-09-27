<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Support\SlugField;
use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Badge;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductVideo;
use App\Models\StockStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager', 'content'];

    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string { return __('admin.resources.products'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.catalog'); }
    public static function getModelLabel(): string { return __('admin.resources.product'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.products'); }

    public static function form(Form $form): Form
    {
        $tr = fn($q) => $q->where('language_id', Language::currentId());

        return $form->schema([
            Forms\Components\Tabs::make('product')->persistTabInQueryString()->columnSpanFull()->tabs([

                Forms\Components\Tabs\Tab::make(__('admin.sections.main'))->icon('heroicon-o-information-circle')->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label(__('admin.fields.sku'))
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('brand_id')
                            ->label(__('admin.resources.brand'))
                            ->options(fn() => Brand::with(['translations' => $tr])->orderBy('sort_order')->get()
                                ->mapWithKeys(fn($b) => [$b->id => $b->translations->first()?->name ?? "#{$b->id}"]))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Select::make('categories')
                            ->label(__('admin.resources.categories'))
                            ->multiple()
                            ->relationship('categories', 'id')
                            ->options(fn() => self::categoryOptions())
                            ->preload(),
                        Forms\Components\Select::make('badges')
                            ->label(__('admin.resources.badges'))
                            ->multiple()
                            ->relationship('badges', 'id')
                            ->options(fn() => Badge::with(['translations' => $tr])->get()
                                ->mapWithKeys(fn($b) => [$b->id => $b->translations->first()?->name ?? "#{$b->id}"]))
                            ->preload(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('admin.fields.show_on_site'))
                            ->helperText(__('admin.hints.show_on_site'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
                ]),

                Forms\Components\Tabs\Tab::make(__('admin.sections.price_stock'))->icon('heroicon-o-banknotes')->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('admin.fields.price'))
                            ->numeric()->minValue(0)
                            ->prefix('₴')
                            ->required(),
                        Forms\Components\TextInput::make('old_price')
                            ->label(__('admin.fields.old_price'))
                            ->helperText(__('admin.hints.old_price'))
                            ->numeric()->minValue(0)
                            ->prefix('₴')
                            ->gt('price')
                            ->nullable(),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label(__('admin.fields.stock_quantity'))
                            ->helperText(__('admin.hints.stock_quantity'))
                            ->numeric()->minValue(0)
                            ->nullable(),
                        Forms\Components\Select::make('stock_status_id')
                            ->label(__('admin.resources.stock_status'))
                            ->helperText(__('admin.hints.stock_status'))
                            ->options(fn() => StockStatus::with(['translations' => $tr])->orderBy('sort_order')->get()
                                ->mapWithKeys(fn($s) => [$s->id => $s->translations->first()?->name ?? $s->code]))
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
                            ->getSearchResultsUsing(fn(string $search, ?Product $record) => self::productSearch($search, $record))
                            ->getOptionLabelUsing(fn($value) => self::productLabel(Product::with(['translations' => $tr])->find($value)))
                            ->searchable()
                            ->nullable(),
                    ])->columns(2),
                ]),

                Forms\Components\Tabs\Tab::make(__('admin.sections.media'))->icon('heroicon-o-photo')->schema([
                    Forms\Components\Repeater::make('images')
                        ->label(__('admin.fields.photos'))
                        ->helperText(__('admin.hints.photos'))
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->collapsible()
                        ->grid(3)
                        ->defaultItems(0)
                        ->addActionLabel(__('admin.actions.add_photo'))
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->hiddenLabel()
                                ->disk('public')
                                ->directory('products')
                                ->image()
                                ->imageEditor()
                                ->maxSize(5120)
                                ->required(),
                            Forms\Components\TextInput::make('alt')
                                ->label(__('admin.fields.alt'))
                                ->maxLength(255),
                        ]),
                    Forms\Components\Repeater::make('videos')
                        ->label(__('admin.fields.videos'))
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel(__('admin.actions.add_video'))
                        ->simple(
                            Forms\Components\TextInput::make('youtube_id')
                                ->placeholder('https://www.youtube.com/watch?v=…')
                                ->required()
                                ->rule(fn() => function (string $attribute, $value, \Closure $fail) {
                                    if (! ProductVideo::parseYoutubeId($value)) {
                                        $fail(__('admin.hints.youtube_invalid'));
                                    }
                                })
                                ->dehydrateStateUsing(fn($state) => ProductVideo::parseYoutubeId($state) ?? $state),
                        ),
                ]),

                Forms\Components\Tabs\Tab::make(__('admin.sections.texts_seo'))->icon('heroicon-o-language')->schema([
                    Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                        Language::query()->where('is_active', true)->orderBy('id')->get()
                            ->map(fn($lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                                Forms\Components\TextInput::make($lang->code . '_name')
                                    ->label(__('admin.fields.name'))
                                    ->required($lang->is_default)
                                    ->maxLength(255),
                                SlugField::make($lang, 'product_translations', 'product_id'),
                                Forms\Components\RichEditor::make($lang->code . '_description')
                                    ->label(__('admin.fields.description'))
                                    ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make($lang->code . '_warning_text')
                                    ->label(__('admin.fields.warning_text'))
                                    ->helperText(__('admin.hints.warning_text'))
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Forms\Components\Fieldset::make('SEO')->schema([
                                    Forms\Components\TextInput::make($lang->code . '_meta_title')
                                        ->label(__('admin.fields.meta_title'))
                                        ->helperText(__('admin.hints.meta_title'))
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make($lang->code . '_meta_description')
                                        ->label(__('admin.fields.meta_description'))
                                        ->helperText(__('admin.hints.meta_description'))
                                        ->rows(2)
                                        ->maxLength(500),
                                ])->columns(1),
                            ])->columns(2))->toArray()
                    ),
                ]),
            ]),
        ]);
    }

    /** Категорії з підкатегоріями у вигляді «Батьківська › Дочірня». */
    public static function categoryOptions(): array
    {
        $cats = Category::with(['translations' => fn($q) => $q->where('language_id', Language::currentId())])
            ->orderBy('sort_order')->get()->keyBy('id');
        $name = fn($c) => $c->translations->first()?->name ?? "#{$c->id}";

        return $cats->mapWithKeys(fn($c) => [
            $c->id => ($c->parent_id && $cats->has($c->parent_id) ? $name($cats[$c->parent_id]) . ' › ' : '') . $name($c),
        ])->sort()->all();
    }

    private static function productLabel(?Product $p): ?string
    {
        return $p ? $p->sku . ' — ' . ($p->translations->first()?->name ?? '') : null;
    }

    private static function productSearch(string $search, ?Product $record): array
    {
        return Product::with(['translations' => fn($q) => $q->where('language_id', Language::currentId())])
            ->when($record, fn($q) => $q->whereKeyNot($record->id))
            ->where(fn($q) => $q->whereLike('sku', "%{$search}%")
                ->orWhereHas('translations', fn($t) => $t->whereLike('name', "%{$search}%")))
            ->limit(30)->get()
            ->mapWithKeys(fn($p) => [$p->id => self::productLabel($p)])->all();
    }

    public static function table(Table $table): Table
    {
        $name = fn($translations) => $translations->firstWhere('language_id', Language::currentId())?->name;

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumb')
                    ->label('')
                    ->disk('public')
                    ->getStateUsing(fn(Product $record) => $record->images->first()?->path)
                    ->square()
                    ->size(44),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn(Product $record) => $name($record->translations) ?? '—')
                    ->description(fn(Product $record) => $record->sku)
                    ->searchable(query: fn($query, string $search) => $query->where(fn($q) => $q
                        ->whereLike('sku', "%{$search}%")
                        ->orWhereHas('translations', fn($t) => $t->whereLike('name', "%{$search}%"))))
                    ->wrap(),
                Tables\Columns\TextColumn::make('brand')
                    ->label(__('admin.resources.brand'))
                    ->getStateUsing(fn(Product $record) => $record->brand ? $name($record->brand->translations) : null)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('admin.fields.price'))
                    ->money('UAH')
                    ->description(fn(Product $record) => $record->old_price ? '₴' . number_format((float) $record->old_price, 2, '.', ' ') : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(__('admin.fields.stock_quantity'))
                    ->placeholder('—')
                    ->badge()
                    ->color(fn($state) => $state === null ? 'gray' : ($state > 0 ? 'success' : 'warning'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label(__('admin.resources.brand'))
                    ->options(fn() => Brand::with('translations')->get()->mapWithKeys(fn($b) => [$b->id => $name($b->translations) ?? "#{$b->id}"]))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('admin.resources.category'))
                    ->options(fn() => self::categoryOptions())
                    ->query(fn($query, array $data) => $query->when($data['value'] ?? null,
                        fn($q, $id) => $q->whereHas('categories', fn($c) => $c->where('categories.id', $id)))),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('admin.fields.show_on_site')),
                Tables\Filters\TernaryFilter::make('in_stock')
                    ->label(__('admin.filters.in_stock'))
                    ->queries(
                        true: fn($query) => $query->where('stock_quantity', '>', 0),
                        false: fn($query) => $query->where(fn($w) => $w->whereNull('stock_quantity')->orWhere('stock_quantity', 0)),
                    ),
                Tables\Filters\Filter::make('sale')->label(__('admin.filters.sale'))->query(fn($query) => $query->whereNotNull('old_price'))->toggle(),
                Tables\Filters\Filter::make('no_photo')->label(__('admin.filters.no_photo'))->query(fn($query) => $query->doesntHave('images'))->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Product $record) => ($slug = $record->translations->firstWhere('language_id', Language::currentId())?->slug) ? url('/p/' . $slug) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Product $record) => $record->is_active),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->label(__('admin.actions.duplicate'))
                    ->excludeAttributes(['sku'])
                    ->form([Forms\Components\TextInput::make('sku')->label(__('admin.fields.sku'))->required()->unique('products', 'sku')])
                    ->beforeReplicaSaved(fn(Product $replica, array $data) => $replica->fill(['sku' => $data['sku'], 'is_active' => false]))
                    ->after(fn(Product $replica, Product $record) => self::copyRelations($record, $replica))
                    ->successRedirectUrl(fn(Product $replica) => self::getUrl('edit', ['record' => $replica])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('show')->label(__('admin.actions.show_on_site'))->icon('heroicon-o-eye')
                        ->action(fn($records) => $records->each->update(['is_active' => true]))->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('hide')->label(__('admin.actions.hide_from_site'))->icon('heroicon-o-eye-slash')
                        ->action(fn($records) => $records->each->update(['is_active' => false]))->deselectRecordsAfterCompletion(),
                    TranslateAction::makeBulk(['name', 'slug', 'description', 'warning_text', 'meta_title', 'meta_description']),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Копія товару: переклади (slug з суфіксом), категорії, бейджі, характеристики. Фото не копіюються. */
    private static function copyRelations(Product $from, Product $to): void
    {
        foreach ($from->translations as $t) {
            $to->translations()->create([
                ...$t->only(['language_id', 'name', 'description', 'warning_text', 'meta_title', 'meta_description']),
                'slug' => $t->slug ? $t->slug . '-' . $to->id : null,
            ]);
        }
        $to->categories()->sync($from->categories()->pluck('categories.id'));
        $to->badges()->sync($from->badges()->pluck('badges.id'));
        $to->attributeValues()->sync($from->attributeValues()->pluck('attribute_values.id'));
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
            ->with(['translations', 'brand.translations', 'images']);
    }
}
