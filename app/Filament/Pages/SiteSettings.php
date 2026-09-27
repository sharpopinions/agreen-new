<?php

namespace App\Filament\Pages;

use App\Models\Language;
use App\Support\SiteSettings as Settings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/** Контакти, графік, соцмережі — показуються в хедері, футері, мегаменю й на сторінці «Контакти». */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string { return __('admin.site.title'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.settings'); }
    public function getTitle(): string { return __('admin.site.title'); }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'content'], true);
    }

    public function mount(): void
    {
        $this->form->fill(Settings::all());
    }

    private function languages()
    {
        return Language::query()->where('is_active', true)->orderBy('id')->get();
    }

    /** Поле з перекладами: вкладка на кожну активну мову (name.uk, name.en…). */
    private function translatable(string $field, string $label, bool $textarea = false): Forms\Components\Tabs
    {
        return Forms\Components\Tabs::make($label)->tabs(
            $this->languages()->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->code)
                ->label(strtoupper($lang->code))
                ->schema([
                    ($textarea ? Forms\Components\Textarea::make("{$field}.{$lang->code}")->rows(3) : Forms\Components\TextInput::make("{$field}.{$lang->code}"))
                        ->label($label)
                        ->required($lang->is_default)
                        ->maxLength(500),
                ]))->all()
        );
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Section::make(__('admin.site.contacts'))->schema([
                Forms\Components\TextInput::make('phone')->label(__('admin.site.phone'))
                    ->helperText(__('admin.site.phone_hint'))->required()->maxLength(50),
                Forms\Components\TagsInput::make('emails')->label('Email')
                    ->helperText(__('admin.site.emails_hint'))
                    ->nestedRecursiveRules(['email']),
                Forms\Components\Repeater::make('departments')->label(__('admin.site.departments'))
                    ->schema([
                        ...$this->languages()->map(fn(Language $lang) => Forms\Components\TextInput::make("name.{$lang->code}")
                            ->label(__('admin.fields.name') . ' (' . $lang->code . ')')
                            ->required($lang->is_default)->maxLength(100))->all(),
                        Forms\Components\TagsInput::make('phones')->label(__('admin.site.phones'))->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->itemLabel(fn(array $state) => $state['name']['uk'] ?? null)
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make(__('admin.site.address_schedule'))->schema([
                $this->translatable('address', __('admin.site.address')),
                $this->translatable('schedule_short', __('admin.site.schedule_short')),
                $this->translatable('schedule', __('admin.site.schedule'), textarea: true),
                Forms\Components\TextInput::make('map_url')->label(__('admin.site.map_url'))
                    ->helperText(__('admin.site.map_url_hint'))->url()->maxLength(1000),
            ])->columns(2),

            Forms\Components\Section::make(__('admin.site.footer'))->schema([
                $this->translatable('footer_text', __('admin.site.footer_text'), textarea: true),
            ]),

            Forms\Components\Section::make(__('admin.site.socials'))->schema(
                collect(Settings::SOCIALS)->map(fn($key) => Forms\Components\TextInput::make("socials.{$key}")
                    ->label(ucfirst($key))->url()->maxLength(255)->placeholder('https://'))->all()
            )->columns(3)->collapsible(),
        ]);
    }

    public function save(): void
    {
        Settings::save($this->form->getState());

        Notification::make()->success()->title(__('admin.site.saved'))->send();
    }
}
