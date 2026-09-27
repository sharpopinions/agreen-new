<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Support\SlugField;
use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\AttributeDefinition;
use App\Models\Category;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager', 'content'];

    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('admin.resources.categories'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.catalog'); }
    public static function getModelLabel(): string { return __('admin.resources.category'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.categories'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\Select::make('parent_id')
                    ->label(__('admin.fields.parent_category'))
                    ->options(fn(?Category $record) => collect(ProductResource::categoryOptions())
                        ->except($record ? [$record->id, ...$record->descendantIds()] : [])
                        ->all())
                    ->placeholder(__('admin.fields.root_category'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->default(0),
                Forms\Components\FileUpload::make('image')
                    ->label(__('admin.fields.image'))
                    ->helperText(__('admin.hints.category_image'))
                    ->disk('public')
                    ->directory('categories')
                    ->image()
                    ->imageEditor()
                    ->maxSize(5120),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.fields.show_on_site'))
                    ->default(true)
                    ->inline(false),
            ])->columns(2),

            Forms\Components\Section::make(__('admin.attributes.filters'))
                ->description(__('admin.attributes.filters_hint'))
                ->schema([
                    Forms\Components\Repeater::make('filter_rows')
                        ->hiddenLabel()
                        ->schema([
                            Forms\Components\Select::make('attribute_definition_id')
                                ->label(__('admin.resources.attribute'))
                                ->options(fn() => AttributeDefinition::with('translations')->where('is_filterable', true)->orderBy('sort_order')->get()
                                    ->mapWithKeys(fn($d) => [$d->id => $d->name]))
                                ->required()
                                ->searchable()
                                ->distinct()
                                ->live()
                                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('display_type',
                                    AttributeDefinition::DEFAULT_DISPLAY[AttributeDefinition::find($state)?->type] ?? 'checkbox')),
                            Forms\Components\Select::make('display_type')
                                ->label(__('admin.attributes.display'))
                                ->options(collect(AttributeDefinition::DISPLAY_TYPES)->mapWithKeys(fn($t) => [$t => __('admin.attributes.displays.' . $t)]))
                                ->default('checkbox')
                                ->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderable()
                        ->addActionLabel(__('admin.attributes.add_filter')),
                ])
                ->collapsible(),

            Forms\Components\Tabs::make(__('admin.sections.translations'))
                ->tabs(
                    Language::query()->where('is_active', true)->orderBy('id')->get()
                        ->map(fn($lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                            Forms\Components\TextInput::make($lang->code . '_name')
                                ->label(__('admin.fields.name'))
                                ->required($lang->is_default)
                                ->maxLength(255),
                            SlugField::make($lang, 'category_translations', 'category_id'),
                            Forms\Components\Textarea::make($lang->code . '_short_description')
                                ->label(__('admin.fields.short_description'))
                                ->helperText(__('admin.hints.category_short_description'))
                                ->rows(2)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make($lang->code . '_description')
                                ->label(__('admin.fields.seo_text'))
                                ->helperText(__('admin.hints.category_description'))
                                ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                                ->columnSpanFull(),
                            Forms\Components\Fieldset::make('SEO')->schema([
                                Forms\Components\TextInput::make($lang->code . '_meta_title')
                                    ->label(__('admin.fields.meta_title'))
                                    ->helperText(__('admin.hints.meta_title_category'))
                                    ->maxLength(255),
                                Forms\Components\Textarea::make($lang->code . '_meta_description')
                                    ->label(__('admin.fields.meta_description'))
                                    ->helperText(__('admin.hints.meta_description'))
                                    ->rows(2)
                                    ->maxLength(500),
                            ])->columns(1),
                        ])->columns(2))->toArray()
                )
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $name = fn($translations) => $translations->firstWhere('language_id', Language::currentId())?->name;

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->disk('public')->square()->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn(Category $record) => $name($record->translations) ?? '—')
                    ->description(fn(Category $record) => $record->parent ? '↳ ' . $name($record->parent->translations) : null)
                    ->searchable(query: fn($query, $search) => $query->whereHas('translations', fn($q) => $q->whereLike('name', "%{$search}%"))),
                Tables\Columns\TextColumn::make('products_count')
                    ->label(__('admin.resources.products'))
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(__('admin.fields.parent_category'))
                    ->options(fn() => Category::whereNull('parent_id')->with('translations')->get()
                        ->mapWithKeys(fn($c) => [$c->id => $name($c->translations) ?? "#{$c->id}"])),
                Tables\Filters\Filter::make('root')->label(__('admin.filters.root_only'))
                    ->query(fn($query) => $query->whereNull('parent_id'))->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Category $record) => ($slug = $record->translations->firstWhere('language_id', Language::currentId())?->slug) ? url('/catalog/' . $slug) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Category $record) => $record->is_active),
                Tables\Actions\EditAction::make(),
                // Категорію з товарами чи підкатегоріями не видаляємо — спершу перенесіть їх
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(Category $record) => $record->products_count > 0 || $record->children()->exists()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    TranslateAction::makeBulk(['name', 'slug', 'short_description', 'description', 'meta_title', 'meta_description']),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['translations', 'parent.translations']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
