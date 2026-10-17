<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\SlugField;
use App\Models\Language;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Послуги (/services): картка в списку і сторінка послуги з цифрами, перевагами, курсами й формою заявки. */
class ServiceResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'content'];

    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string { return __('admin.services.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.services.single'); }
    public static function getPluralModelLabel(): string { return __('admin.services.plural'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('admin.sections.main'))->schema([
                Forms\Components\TextInput::make('icon')->label(__('admin.services.icon'))
                    ->helperText(__('admin.services.icon_hint'))->maxLength(16),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.show_on_site'))->default(true)->inline(false),
                Forms\Components\FileUpload::make('image')->label(__('admin.fields.image'))
                    ->disk('public')->directory('services')->image()->imageEditor()->maxSize(5120)->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                        Forms\Components\TextInput::make($lang->code . '_title')->label(__('admin.pages.title'))->required($lang->is_default)->maxLength(255),
                        SlugField::make($lang, 'service_translations', 'service_id'),
                        Forms\Components\Textarea::make($lang->code . '_summary')->label(__('admin.services.summary'))
                            ->helperText(__('admin.services.summary_hint'))->rows(3)->maxLength(1000)->columnSpanFull(),
                        Forms\Components\Repeater::make($lang->code . '_details')->label(__('admin.services.details'))
                            ->simple(Forms\Components\TextInput::make('text')->required()->maxLength(255))
                            ->defaultItems(0)->addActionLabel(__('admin.pages.add_item'))->columnSpanFull(),
                        Forms\Components\Repeater::make($lang->code . '_stats')->label(__('admin.brands.stats'))
                            ->schema([
                                Forms\Components\TextInput::make('value')->label(__('admin.pages.stat_value'))->required()->maxLength(20),
                                Forms\Components\TextInput::make('label')->label(__('admin.pages.stat_label'))->required()->maxLength(100),
                            ])->columns(2)->defaultItems(0)->maxItems(6)->addActionLabel(__('admin.pages.add_stat'))->columnSpanFull(),
                        Forms\Components\Repeater::make($lang->code . '_benefits')->label(__('admin.services.benefits'))
                            ->schema([
                                Forms\Components\TextInput::make('icon')->label(__('admin.services.icon'))->maxLength(16),
                                Forms\Components\TextInput::make('title')->label(__('admin.pages.heading'))->required()->maxLength(255),
                                Forms\Components\TextInput::make('text')->label(__('admin.pages.text'))->maxLength(500),
                            ])->columns(3)->defaultItems(0)->collapsible()->addActionLabel(__('admin.pages.add_item'))->columnSpanFull(),
                        Forms\Components\Repeater::make($lang->code . '_courses')->label(__('admin.services.courses'))
                            ->helperText(__('admin.services.courses_hint'))
                            ->schema([
                                Forms\Components\TextInput::make('title')->label(__('admin.pages.heading'))->required()->maxLength(255)->columnSpan(2),
                                Forms\Components\TextInput::make('duration')->label(__('admin.services.duration'))->placeholder('24 години')->maxLength(50),
                                Forms\Components\TextInput::make('level')->label(__('admin.services.level'))->placeholder('Середній')->maxLength(50),
                                Forms\Components\TextInput::make('price')->label(__('admin.fields.price'))->placeholder('4 800 ₴')->maxLength(50),
                            ])->columns(5)->defaultItems(0)->collapsible()->reorderable()->addActionLabel(__('admin.pages.add_item'))->columnSpanFull(),
                        Forms\Components\RichEditor::make($lang->code . '_body')->label(__('admin.blog.body'))
                            ->toolbarButtons(['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('icon')->label(''),
                Tables\Columns\TextColumn::make('name')->label(__('admin.pages.title')),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('admin.actions.view_on_site'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Service $record) => ($slug = $record->translations->firstWhere('language_id', Language::currentId())?->slug) ? url('/services/' . $slug) : null)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
