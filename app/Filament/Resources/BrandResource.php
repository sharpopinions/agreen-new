<?php

namespace App\Filament\Resources;

use App\Filament\Actions\TranslateAction;
use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BrandResource extends Resource
{
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
                    ->image()
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
                            Forms\Components\TextInput::make($lang->code . '_slug')
                                ->label(__('admin.fields.slug') . ' (' . $lang->code . ')')
                                ->required($lang->is_default)
                                ->maxLength(255),
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
                Tables\Columns\ImageColumn::make('logo')->label(__('admin.fields.logo')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->getStateUsing(fn($record) => $record->translations->where('language_id', \App\Models\Language::currentId())->first()?->name ?? '—'),
                Tables\Columns\TextColumn::make('sort_order')->label(__('admin.fields.sort_order'))->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('admin.fields.is_active'))->boolean(),
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
        return parent::getEloquentQuery()->with('translations')->where('store_id', 1);
    }
}
