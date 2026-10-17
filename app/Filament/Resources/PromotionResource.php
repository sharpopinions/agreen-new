<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\PromotionResource\Pages;
use App\Filament\Support\SlugField;
use App\Models\Brand;
use App\Models\Language;
use App\Models\Product;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Акції: сторінка /sale/{slug} з товарами акції. */
class PromotionResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager', 'content'];

    protected static ?string $model = Promotion::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string { return __('admin.promotions.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.promotions.single'); }
    public static function getPluralModelLabel(): string { return __('admin.promotions.plural'); }

    public static function form(Form $form): Form
    {
        $tr = fn($q) => $q->where('language_id', Language::currentId());

        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('discount_percent')
                    ->label(__('admin.promotions.discount'))
                    ->helperText(__('admin.promotions.discount_hint'))
                    ->numeric()->minValue(1)->maxValue(99)->suffix('%'),
                Forms\Components\DatePicker::make('starts_at')->label(__('admin.promotions.starts_at'))->native(false)->displayFormat('d.m.Y'),
                Forms\Components\DatePicker::make('ends_at')->label(__('admin.promotions.ends_at'))->native(false)->displayFormat('d.m.Y')
                    ->afterOrEqual('starts_at')
                    ->helperText(__('admin.promotions.ends_at_hint')),
                Forms\Components\FileUpload::make('image')
                    ->label(__('admin.fields.image'))
                    ->disk('public')->directory('promotions')->image()->imageEditor()->maxSize(5120),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                    Forms\Components\Toggle::make('is_active')->label(__('admin.fields.show_on_site'))->default(true),
                ]),
            ])->columns(3),

            Forms\Components\Section::make(__('admin.promotions.products'))
                ->description(__('admin.promotions.products_hint'))
                ->schema([
                    Forms\Components\Select::make('brand_id')
                        ->label(__('admin.resources.brand'))
                        ->options(fn() => Brand::with(['translations' => $tr])->orderBy('sort_order')->get()
                            ->mapWithKeys(fn($b) => [$b->id => $b->translations->first()?->name ?? "#{$b->id}"]))
                        ->searchable(),
                    Forms\Components\Select::make('category_id')
                        ->label(__('admin.resources.category'))
                        ->options(fn() => ProductResource::categoryOptions())
                        ->searchable(),
                    Forms\Components\Select::make('products')
                        ->label(__('admin.promotions.selected_products'))
                        ->relationship('products', 'sku')
                        ->multiple()
                        ->searchable()
                        ->getSearchResultsUsing(fn(string $search) => Product::with(['translations' => $tr])
                            ->where(fn($q) => $q->whereLike('sku', "%{$search}%")
                                ->orWhereHas('translations', fn($t) => $t->whereLike('name', "%{$search}%")))
                            ->limit(30)->get()
                            ->mapWithKeys(fn($p) => [$p->id => $p->sku . ' — ' . ($p->translations->first()?->name ?? '')]))
                        ->getOptionLabelFromRecordUsing(fn(Product $record) => $record->sku . ' — ' . ($record->translations()->where('language_id', Language::currentId())->value('name') ?? ''))
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                        Forms\Components\TextInput::make($lang->code . '_title')
                            ->label(__('admin.pages.title'))
                            ->required($lang->is_default)
                            ->maxLength(255),
                        SlugField::make($lang, 'promotion_translations', 'promotion_id'),
                        Forms\Components\RichEditor::make($lang->code . '_description')
                            ->label(__('admin.fields.description'))
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                            ->columnSpanFull(),
                        Forms\Components\Fieldset::make('SEO')->schema([
                            Forms\Components\TextInput::make($lang->code . '_meta_title')->label(__('admin.fields.meta_title'))->maxLength(255),
                            Forms\Components\Textarea::make($lang->code . '_meta_description')->label(__('admin.fields.meta_description'))->rows(2)->maxLength(500),
                        ])->columns(1),
                    ])->columns(2))->all()
            )->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->disk('public')->size(40),
                Tables\Columns\TextColumn::make('name')->label(__('admin.pages.title'))->wrap()
                    ->searchable(query: fn($query, string $search) => $query->whereHas('translations', fn($t) => $t->whereLike('title', "%{$search}%"))),
                Tables\Columns\TextColumn::make('discount_percent')->label(__('admin.promotions.discount'))
                    ->formatStateUsing(fn($state) => $state ? "−{$state}%" : '—')->badge()->color('danger'),
                Tables\Columns\TextColumn::make('ends_at')->label(__('admin.promotions.ends_at'))->date('d.m.Y')->placeholder('—')->sortable()
                    ->color(fn(Promotion $record) => $record->ends_at?->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('state')->label(__('admin.promotions.state'))
                    ->getStateUsing(fn(Promotion $record) => match (true) {
                        ! $record->is_active                    => 'off',
                        (bool) $record->starts_at?->isFuture()  => 'scheduled',
                        $record->ends_at && $record->ends_at->endOfDay()->isPast() => 'ended',
                        default                                 => 'running',
                    })
                    ->formatStateUsing(fn($state) => __('admin.promotions.states.' . $state))
                    ->badge()
                    ->color(fn($state) => ['running' => 'success', 'scheduled' => 'info', 'ended' => 'gray', 'off' => 'gray'][$state]),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\Filter::make('running')->label(__('admin.promotions.states.running'))
                    ->query(fn($query) => $query->running())->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Promotion $record) => ($slug = $record->translations->firstWhere('language_id', Language::currentId())?->slug) ? url('/sale/' . $slug) : null)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit'   => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
