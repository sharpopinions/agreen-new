<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Filament\Support\SlugField;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Статті й події блогу (/blog). Подія має дату, місце, програму, спікерів і форму реєстрації. */
class BlogPostResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'content'];

    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string { return __('admin.blog.posts'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.blog.post'); }
    public static function getPluralModelLabel(): string { return __('admin.blog.posts'); }

    public static function form(Form $form): Form
    {
        $isEvent = fn(Get $get) => $get('type') === 'event';

        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\ToggleButtons::make('type')
                    ->label(__('admin.blog.type'))
                    ->options(['article' => __('admin.blog.types.article'), 'event' => __('admin.blog.types.event')])
                    ->icons(['article' => 'heroicon-o-document-text', 'event' => 'heroicon-o-calendar-days'])
                    ->default('article')
                    ->inline()
                    ->required()
                    ->live(),
                Forms\Components\Select::make('blog_category_id')
                    ->label(__('admin.blog.category'))
                    ->options(fn() => BlogCategory::with('translations')->orderBy('sort_order')->get()->mapWithKeys(fn($c) => [$c->id => $c->name])),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label(__('admin.blog.published_at'))
                    ->helperText(__('admin.blog.published_at_hint'))
                    ->native(false)->displayFormat('d.m.Y H:i')->seconds(false)
                    ->default(now()),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.show_on_site'))->default(true)->inline(false),
                Forms\Components\FileUpload::make('image')
                    ->label(__('admin.blog.image'))
                    ->disk('public')->directory('blog')->image()->imageEditor()->maxSize(5120)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make(__('admin.blog.event'))->schema([
                Forms\Components\DateTimePicker::make('event_starts_at')->label(__('admin.blog.event_starts_at'))
                    ->native(false)->displayFormat('d.m.Y H:i')->seconds(false)->required($isEvent),
                Forms\Components\DateTimePicker::make('event_ends_at')->label(__('admin.blog.event_ends_at'))
                    ->native(false)->displayFormat('d.m.Y H:i')->seconds(false)->after('event_starts_at'),
                Forms\Components\TextInput::make('event_seats')->label(__('admin.blog.event_seats'))->numeric()->minValue(1),
            ])->columns(3)->visible($isEvent),

            Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                        Forms\Components\TextInput::make($lang->code . '_title')->label(__('admin.pages.title'))->required($lang->is_default)->maxLength(255),
                        SlugField::make($lang, 'blog_post_translations', 'blog_post_id'),
                        Forms\Components\Textarea::make($lang->code . '_excerpt')->label(__('admin.blog.excerpt'))
                            ->helperText(__('admin.blog.excerpt_hint'))->rows(2)->maxLength(500)->columnSpanFull(),
                        Forms\Components\RichEditor::make($lang->code . '_body')->label(__('admin.blog.body'))
                            ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'link', 'attachFiles', 'undo', 'redo'])
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('blog/content')
                            ->columnSpanFull(),
                        // Подія
                        Forms\Components\TextInput::make($lang->code . '_event_place')->label(__('admin.blog.event_place'))->maxLength(255)->visible($isEvent),
                        Forms\Components\TextInput::make($lang->code . '_event_price')->label(__('admin.blog.event_price'))
                            ->placeholder('Безкоштовно')->maxLength(100)->visible($isEvent),
                        Forms\Components\Repeater::make($lang->code . '_program')->label(__('admin.blog.program'))
                            ->schema([
                                Forms\Components\TextInput::make('time')->label(__('admin.blog.program_time'))->placeholder('10:00 – 11:00')->required()->maxLength(30),
                                Forms\Components\TextInput::make('topic')->label(__('admin.blog.program_topic'))->required()->maxLength(255),
                            ])->columns(2)->defaultItems(0)->reorderable()->addActionLabel(__('admin.pages.add_item'))
                            ->visible($isEvent)->columnSpanFull(),
                        Forms\Components\Repeater::make($lang->code . '_speakers')->label(__('admin.blog.speakers'))
                            ->schema([
                                Forms\Components\TextInput::make('name')->label(__('admin.fields.name'))->required()->maxLength(255),
                                Forms\Components\TextInput::make('role')->label(__('admin.blog.speaker_role'))->maxLength(255),
                            ])->columns(2)->defaultItems(0)->reorderable()->addActionLabel(__('admin.pages.add_item'))
                            ->visible($isEvent)->columnSpanFull(),
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
                    ->description(fn(BlogPost $record) => $record->category?->name)
                    ->searchable(query: fn($query, string $search) => $query->whereHas('translations', fn($t) => $t->whereLike('title', "%{$search}%"))),
                Tables\Columns\TextColumn::make('type')->label(__('admin.blog.type'))
                    ->formatStateUsing(fn($state) => __('admin.blog.types.' . $state))->badge()
                    ->color(fn($state) => $state === 'event' ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('published_at')->label(__('admin.blog.published_at'))->dateTime('d.m.Y H:i')->sortable()
                    ->description(fn(BlogPost $record) => $record->published_at?->isFuture() ? __('admin.blog.scheduled') : null),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label(__('admin.blog.type'))
                    ->options(['article' => __('admin.blog.types.article'), 'event' => __('admin.blog.types.event')]),
                Tables\Filters\SelectFilter::make('blog_category_id')->label(__('admin.blog.category'))
                    ->options(fn() => BlogCategory::with('translations')->get()->mapWithKeys(fn($c) => [$c->id => $c->name])),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(BlogPost $record) => ($slug = $record->translations->firstWhere('language_id', Language::currentId())?->slug) ? url('/blog/' . $slug) : null)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['translations', 'category.translations']);
    }
}
