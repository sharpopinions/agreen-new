<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\PageBlocks;
use App\Filament\Support\SlugField;
use App\Models\Language;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Інформаційні сторінки сайту: Про нас, Доставка та оплата, Оферта, Конфіденційність… */
class PageResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'content'];

    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('admin.pages.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.pages.single'); }
    public static function getPluralModelLabel(): string { return __('admin.pages.plural'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\Select::make('layout')
                    ->label(__('admin.pages.layout'))
                    ->options(collect(Page::LAYOUTS)->mapWithKeys(fn($l) => [$l => __('admin.pages.layouts.' . $l)]))
                    ->helperText(__('admin.pages.layout_hint'))
                    ->default('landing')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.show_on_site'))->default(true)->inline(false),
            ])->columns(3),

            Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                        Forms\Components\TextInput::make($lang->code . '_title')
                            ->label(__('admin.pages.title'))
                            ->required($lang->is_default)
                            ->maxLength(255),
                        SlugField::make($lang, 'page_translations', 'page_id')
                            ->notIn(Page::RESERVED_SLUGS)
                            ->validationMessages(['not_in' => __('admin.pages.slug_reserved')])
                            ->prefix(url('/') . '/'),
                        PageBlocks::make($lang->code . '_blocks'),
                        Forms\Components\Fieldset::make('SEO')->schema([
                            Forms\Components\TextInput::make($lang->code . '_meta_title')
                                ->label(__('admin.fields.meta_title'))->maxLength(255),
                            Forms\Components\Textarea::make($lang->code . '_meta_description')
                                ->label(__('admin.fields.meta_description'))->rows(2)->maxLength(500),
                        ])->columns(1),
                    ])->columns(2))->all()
            )->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.pages.title'))
                    ->description(fn(Page $record) => ($slug = self::slug($record)) ? '/' . $slug : null),
                Tables\Columns\TextColumn::make('layout')->label(__('admin.pages.layout'))
                    ->formatStateUsing(fn($state) => __('admin.pages.layouts.' . $state))->badge()->color('gray'),
                Tables\Columns\TextColumn::make('updated_at')->label(__('admin.pages.updated'))->since()->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Page $record) => ($slug = self::slug($record)) ? url('/' . $slug) : null)
                    ->openUrlInNewTab()
                    ->visible(fn(Page $record) => $record->is_active),
                Tables\Actions\EditAction::make(),
                // Системні сторінки (футер, меню посилаються на них) не видаляємо
                Tables\Actions\DeleteAction::make()->hidden(fn(Page $record) => $record->key !== null),
            ]);
    }

    private static function slug(Page $page): ?string
    {
        return $page->translations->firstWhere('language_id', Language::currentId())?->slug;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
