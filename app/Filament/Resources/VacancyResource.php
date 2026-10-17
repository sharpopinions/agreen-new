<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\VacancyResource\Pages;
use App\Models\Language;
use App\Models\Vacancy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Вакансії (/vacancies). Відгуки кандидатів приходять у «Заявки». */
class VacancyResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'content'];

    protected static ?string $model = Vacancy::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string { return __('admin.vacancies.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.vacancies.single'); }
    public static function getPluralModelLabel(): string { return __('admin.vacancies.plural'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label(__('admin.vacancies.open'))->default(true)->inline(false),
            ])->columns(2),
            Forms\Components\Tabs::make(__('admin.sections.translations'))->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema([
                        Forms\Components\TextInput::make($lang->code . '_title')->label(__('admin.vacancies.title'))->required($lang->is_default)->maxLength(255)->columnSpanFull(),
                        Forms\Components\TextInput::make($lang->code . '_department')->label(__('admin.vacancies.department'))->maxLength(255),
                        Forms\Components\TextInput::make($lang->code . '_city')->label(__('admin.vacancies.city'))->maxLength(255),
                        Forms\Components\TextInput::make($lang->code . '_employment')->label(__('admin.vacancies.employment'))
                            ->placeholder('Повна зайнятість')->maxLength(255),
                        Forms\Components\Textarea::make($lang->code . '_description')->label(__('admin.vacancies.description'))->rows(3)->maxLength(2000)->columnSpanFull(),
                        Forms\Components\Repeater::make($lang->code . '_requirements')->label(__('admin.vacancies.requirements'))
                            ->simple(Forms\Components\TextInput::make('text')->required()->maxLength(255))
                            ->defaultItems(0)->reorderable()->addActionLabel(__('admin.pages.add_item'))->columnSpanFull(),
                    ])->columns(3))->all()
            )->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.vacancies.title'))
                    ->description(fn(Vacancy $record) => collect([
                        $record->currentTranslation()?->department, $record->currentTranslation()?->city,
                    ])->filter()->implode(' · ')),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.vacancies.open')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVacancies::route('/'),
            'create' => Pages\CreateVacancy::route('/create'),
            'edit'   => Pages\EditVacancy::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }
}
