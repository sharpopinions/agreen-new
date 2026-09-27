<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\AttributeDefinitionResource\Pages;
use App\Filament\Support\TranslationTabs;
use App\Models\AttributeDefinition;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Характеристики товарів і їхні значення (EAV) — основа фільтрів каталогу. */
class AttributeDefinitionResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager', 'content'];

    protected static ?string $model = AttributeDefinition::class;
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string { return __('admin.resources.attributes'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.catalog'); }
    public static function getModelLabel(): string { return __('admin.resources.attribute'); }
    public static function getPluralModelLabel(): string { return __('admin.resources.attributes'); }

    public static function typeOptions(): array
    {
        return collect(AttributeDefinition::TYPES)->mapWithKeys(fn($t) => [$t => __('admin.attributes.types.' . $t)])->all();
    }

    public static function form(Form $form): Form
    {
        $languages = Language::query()->where('is_active', true)->orderBy('id')->get();

        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\Select::make('type')
                    ->label(__('admin.attributes.type'))
                    ->helperText(__('admin.attributes.type_hint'))
                    ->options(self::typeOptions())
                    ->default('select')
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_filterable')->label(__('admin.attributes.is_filterable'))
                    ->helperText(__('admin.attributes.is_filterable_hint'))->default(true),
                Forms\Components\Toggle::make('is_comparable')->label(__('admin.attributes.is_comparable'))->default(false),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true),
            ])->columns(2),

            TranslationTabs::make(['name' => true]),

            Forms\Components\Section::make(__('admin.attributes.values'))
                ->description(fn(Get $get) => __('admin.attributes.values_hint.' . ($get('type') ?? 'select')))
                ->schema([
                    Forms\Components\Repeater::make('value_rows')
                        ->hiddenLabel()
                        ->schema([
                            Forms\Components\Hidden::make('id'),
                            ...$languages->map(fn(Language $lang) => Forms\Components\TextInput::make('name_' . $lang->code)
                                ->label(__('admin.fields.name') . ' (' . $lang->code . ')')
                                ->required($lang->is_default)
                                ->maxLength(255))->all(),
                            Forms\Components\TextInput::make('raw')
                                ->label(__('admin.attributes.number'))
                                ->numeric()
                                ->required()
                                ->visible(fn(Get $get) => $get('../../type') === 'number'),
                            Forms\Components\ColorPicker::make('raw')
                                ->label(__('admin.attributes.color'))
                                ->required()
                                ->visible(fn(Get $get) => $get('../../type') === 'color'),
                        ])
                        ->columns($languages->count() + 1)
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel(__('admin.attributes.add_value'))
                        ->itemLabel(fn(array $state) => $state['name_uk'] ?? null)
                        ->collapsible(),
                ])
                ->hidden(fn(Get $get) => $get('type') === 'boolean'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name'))
                    ->searchable(query: fn($query, string $search) => $query->whereHas('translations', fn($t) => $t->whereLike('name', "%{$search}%"))),
                Tables\Columns\TextColumn::make('type')->label(__('admin.attributes.type'))
                    ->formatStateUsing(fn($state) => self::typeOptions()[$state] ?? $state)->badge()->color('gray'),
                Tables\Columns\TextColumn::make('values_count')->label(__('admin.attributes.values'))->counts('values'),
                Tables\Columns\TextColumn::make('categories_count')->label(__('admin.attributes.in_filters'))->counts('categories'),
                Tables\Columns\IconColumn::make('is_filterable')->label(__('admin.attributes.is_filterable'))->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label(__('admin.attributes.type'))->options(self::typeOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription(__('admin.attributes.delete_warning')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttributeDefinitions::route('/'),
            'create' => Pages\CreateAttributeDefinition::route('/create'),
            'edit'   => Pages\EditAttributeDefinition::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
