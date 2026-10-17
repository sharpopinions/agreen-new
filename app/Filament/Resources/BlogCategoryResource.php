<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Filament\Support\SlugField;
use App\Models\BlogCategory;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Рубрики блогу — вкладки на сторінці /blog. */
class BlogCategoryResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'content'];

    protected static ?string $model = BlogCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string { return __('admin.blog.categories'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.blog.category'); }
    public static function getPluralModelLabel(): string { return __('admin.blog.categories'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.show_on_site'))->default(true)->inline(false),
            ])->columns(2),
            Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                        Forms\Components\TextInput::make($lang->code . '_name')->label(__('admin.fields.name'))->required($lang->is_default)->maxLength(255),
                        SlugField::make($lang, 'blog_category_translations', 'blog_category_id'),
                    ])->columns(2))->all()
            )->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name')),
                Tables\Columns\TextColumn::make('posts_count')->label(__('admin.blog.posts'))->counts('posts'),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit'   => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
