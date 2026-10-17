<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Support\SlugField;
use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use App\Models\BrandMedia;
use App\Models\Language;
use App\Models\ProductVideo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager', 'content'];

    protected static ?string $model = Brand::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string { return __('admin.resources.brands'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.catalog'); }
    public static function getModelLabel(): string { return __('admin.resources.brand'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.brands'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\FileUpload::make('logo')
                    ->label(__('admin.fields.logo'))
                    ->disk('public')
                    ->directory('brands')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('sort_order')
                        ->label(__('admin.fields.sort_order'))
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('admin.fields.show_on_site'))
                        ->default(true),
                ]),
            ])->columns(2),

            Forms\Components\Tabs::make(__('admin.sections.translations'))
                ->tabs(
                    Language::query()->where('is_active', true)->orderBy('id')->get()
                        ->map(fn($lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                            Forms\Components\TextInput::make($lang->code . '_name')
                                ->label(__('admin.fields.name'))
                                ->required($lang->is_default)
                                ->maxLength(255),
                            SlugField::make($lang, 'brand_translations', 'brand_id'),
                            Forms\Components\Textarea::make($lang->code . '_short_description')
                                ->label(__('admin.brands.short_description'))
                                ->helperText(__('admin.brands.short_description_hint'))
                                ->rows(3)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make($lang->code . '_description')
                                ->label(__('admin.brands.about'))
                                ->helperText(__('admin.brands.about_hint'))
                                ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make($lang->code . '_stats')
                                ->label(__('admin.brands.stats'))
                                ->helperText(__('admin.brands.stats_hint'))
                                ->schema([
                                    Forms\Components\TextInput::make('value')->label(__('admin.pages.stat_value'))->required()->maxLength(20),
                                    Forms\Components\TextInput::make('label')->label(__('admin.pages.stat_label'))->required()->maxLength(60),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->maxItems(6)
                                ->addActionLabel(__('admin.pages.add_stat'))
                                ->columnSpanFull(),
                            Forms\Components\Fieldset::make('SEO')->schema([
                                Forms\Components\TextInput::make($lang->code . '_meta_title')->label(__('admin.fields.meta_title'))->maxLength(255),
                                Forms\Components\Textarea::make($lang->code . '_meta_description')->label(__('admin.fields.meta_description'))->rows(2)->maxLength(500),
                            ])->columns(1),
                        ])->columns(2))->toArray()
                )
                ->columnSpanFull(),

            Forms\Components\Section::make(__('admin.brands.media'))
                ->description(__('admin.brands.media_hint'))
                ->schema([
                    Forms\Components\Repeater::make('media')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(0)
                        ->itemLabel(fn(array $state) => $state['title'] ?? null)
                        ->addActionLabel(__('admin.brands.add_media'))
                        ->mutateRelationshipDataBeforeFillUsing(fn(array $data) => $data + [
                            'file'    => $data['type'] !== 'video' ? $data['path'] : null,
                            'youtube' => $data['type'] === 'video' ? 'https://youtu.be/' . $data['path'] : null,
                        ])
                        ->mutateRelationshipDataBeforeCreateUsing(fn(array $data) => self::mediaPath($data))
                        ->mutateRelationshipDataBeforeSaveUsing(fn(array $data) => self::mediaPath($data))
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label(__('admin.brands.media_type'))
                                ->options(collect(BrandMedia::TYPES)->mapWithKeys(fn($t) => [$t => __('admin.brands.media_types.' . $t)]))
                                ->default('image')
                                ->required()
                                ->live(),
                            Forms\Components\TextInput::make('title')
                                ->label(__('admin.brands.media_title'))
                                ->required()
                                ->maxLength(255),
                            // Файл (фото чи документ) і посилання YouTube — окремі поля; у БД обидва в path
                            Forms\Components\FileUpload::make('file')
                                ->label(__('admin.brands.file'))
                                ->disk('public')
                                ->directory('brands/media')
                                ->preserveFilenames()
                                ->acceptedFileTypes(fn(Forms\Get $get) => $get('type') === 'image'
                                    ? ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
                                    : ['application/pdf', 'application/zip', 'application/x-zip-compressed',
                                        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                ->maxSize(51200)
                                ->required(fn(Forms\Get $get) => $get('type') !== 'video')
                                ->visible(fn(Forms\Get $get) => $get('type') !== 'video')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('youtube')
                                ->label(__('admin.fields.videos'))
                                ->placeholder('https://www.youtube.com/watch?v=…')
                                ->required(fn(Forms\Get $get) => $get('type') === 'video')
                                ->rule(fn(Forms\Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($get('type') === 'video' && ! ProductVideo::parseYoutubeId($value)) {
                                        $fail(__('admin.hints.youtube_invalid'));
                                    }
                                })
                                ->visible(fn(Forms\Get $get) => $get('type') === 'video')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                ->collapsible(),
        ]);
    }

    /** Поля форми file / youtube → колонка path. */
    private static function mediaPath(array $data): array
    {
        $data['path'] = $data['type'] === 'video'
            ? ProductVideo::parseYoutubeId($data['youtube'] ?? '')
            : (is_array($data['file'] ?? null) ? reset($data['file']) : ($data['file'] ?? null));
        unset($data['file'], $data['youtube']);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label('')->disk('public')->square()->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn($record) => $record->translations->firstWhere('language_id', Language::currentId())?->name ?? '—')
                    ->searchable(query: fn($query, string $search) => $query->whereHas('translations', fn($t) => $t->whereLike('name', "%{$search}%"))),
                Tables\Columns\TextColumn::make('products_count')->label(__('admin.resources.products'))->counts('products')->sortable(),
                Tables\Columns\TextColumn::make('media_count')->label(__('admin.brands.media'))->counts('media')->toggleable(),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Brand $record) => ($slug = $record->translations->firstWhere('language_id', Language::currentId())?->slug) ? url('/brands/' . $slug) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Brand $record) => $record->is_active),
                Tables\Actions\EditAction::make(),
                // Бренд з товарами не видаляємо — спершу перенесіть товари
                Tables\Actions\DeleteAction::make()->hidden(fn(Brand $record) => $record->products()->exists()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    TranslateAction::makeBulk(['name', 'slug', 'short_description', 'description']),
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
            'index'  => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit'   => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
