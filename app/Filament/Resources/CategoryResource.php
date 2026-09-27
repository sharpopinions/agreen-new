<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Support\SlugField;
use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
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
                    ->label('Батьківська категорія')
                    ->options(fn() => Category::with(['translations' => fn($q) => $q->where('language_id', \App\Models\Language::currentId())])
                        ->where('store_id', 1)
                        ->get()
                        ->mapWithKeys(fn($c) => [$c->id => $c->translations->first()?->name ?? "Категорія #{$c->id}"]))
                    ->placeholder('— Коренева категорія —')
                    ->nullable(),
                Forms\Components\TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->default(true),
            ])->columns(2),

            Forms\Components\Tabs::make(__('admin.sections.translations'))
                ->tabs(
                    \App\Models\Language::where('store_id', 1)->where('is_active', true)->orderBy('id')->get()
                        ->map(fn($lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                            Forms\Components\TextInput::make($lang->code . '_name')
                                ->label(__('admin.fields.name') . ' (' . $lang->code . ')')
                                ->required($lang->is_default)
                                ->maxLength(255),
                            SlugField::make($lang, 'category_translations', 'category_id'),
                            Forms\Components\Textarea::make($lang->code . '_description')
                                ->label(__('admin.fields.description') . ' (' . $lang->code . ')')
                                ->rows(4),
                        ]))->toArray()
                )
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn($record) => $record->translations->where('language_id', \App\Models\Language::currentId())->first()?->name ?? '—')
                    ->searchable(query: fn($query, $search) => $query->whereHas('translations', fn($q) => $q->where('name', 'like', "%{$search}%"))),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Батьківська')
                    ->getStateUsing(fn($record) => $record->parent?->translations->where('language_id', \App\Models\Language::currentId())->first()?->name ?? '—'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    TranslateAction::makeBulk(['name', 'slug', 'description']),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['translations', 'parent.translations'])->where('store_id', 1);
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
