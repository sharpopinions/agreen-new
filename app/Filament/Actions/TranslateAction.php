<?php

namespace App\Filament\Actions;

use App\Models\Language;
use App\Services\DeepLTranslationService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TranslateAction
{
    private static array $fieldLabels = [
        'name'        => 'Назва',
        'slug'        => 'Slug',
        'description' => 'Опис',
        'short_description' => 'Короткий опис',
        'warning_text'     => 'Попередження',
        'meta_title'       => 'Meta title',
        'meta_description' => 'Meta description',
    ];

    /**
     * Кнопка "Перекласти" в хедері Edit-сторінки.
     * Показує лише мови де є хоча б одне незаповнене поле.
     * Показує які саме поля відсутні для кожної мови.
     */
    public static function makeForPage(array $fields): Action
    {
        return Action::make('translate')
            ->label('Перекласти')
            ->icon('heroicon-o-language')
            ->color('info')
            ->modalHeading('Автопереклад через DeepL')
            ->modalSubmitActionLabel('Перекласти')
            ->form([
                Select::make('target_language_id')
                    ->label('Мова перекладу')
                    ->options(fn($record) => self::getLanguageOptions($record, $fields))
                    ->required()
                    ->helperText('Показані лише мови з незаповненими полями'),

                CheckboxList::make('selected_fields')
                    ->label('Поля для перекладу')
                    ->options(
                        collect($fields)
                            ->mapWithKeys(fn($f) => [$f => self::$fieldLabels[$f] ?? $f])
                            ->toArray()
                    )
                    ->default($fields)
                    ->columns(3),
            ])
            ->action(function (array $data, $livewire) use ($fields) {
                $service = app(DeepLTranslationService::class);

                if (!$service->isConfigured()) {
                    Notification::make()->danger()
                        ->title('DeepL не налаштовано')
                        ->body('Додайте DEEPL_API_KEY до .env файлу')
                        ->send();
                    return;
                }

                $fieldsToTranslate = !empty($data['selected_fields'])
                    ? $data['selected_fields']
                    : $fields;

                $record     = $livewire->record;
                $sourceLang = Language::query()->where('is_default', true)->first();
                $targetLang = Language::find($data['target_language_id']);

                $sourceTranslation = $record->translations->firstWhere('language_id', $sourceLang->id);

                if (!$sourceTranslation || empty($sourceTranslation->name)) {
                    Notification::make()->warning()
                        ->title('Немає вихідного тексту')
                        ->body("Спочатку заповніть переклад мовою «{$sourceLang->name}»")
                        ->send();
                    return;
                }

                try {
                    $texts = collect($fieldsToTranslate)
                        ->mapWithKeys(fn($f) => [$f => $sourceTranslation->$f ?? ''])
                        ->toArray();

                    $translated = $service->translateBatch($texts, $targetLang->code, $sourceLang->code);

                    $record->translations()->updateOrCreate(
                        ['language_id' => $targetLang->id],
                        $translated
                    );

                    $fieldNames = collect($fieldsToTranslate)
                        ->map(fn($f) => self::$fieldLabels[$f] ?? $f)
                        ->implode(', ');

                    Notification::make()->success()
                        ->title("Перекладено на «{$targetLang->name}»!")
                        ->body("Поля: {$fieldNames}")
                        ->send();

                    // Оновлюємо форму без редиректу — перезавантажуємо Livewire-стан
                    $livewire->refillFormAfterTranslation();
                } catch (\Exception $e) {
                    Notification::make()->danger()
                        ->title('Помилка DeepL')
                        ->body($e->getMessage())
                        ->send();
                }
            });
    }

    /**
     * Bulk action "Перекласти" в таблиці.
     * Дозволяє перекласти декілька записів за раз.
     */
    public static function makeBulk(array $fields): BulkAction
    {
        return BulkAction::make('translate')
            ->label('Перекласти')
            ->icon('heroicon-o-language')
            ->color('info')
            ->modalHeading('Масовий автопереклад через DeepL')
            ->modalDescription('Якщо переклад для мови вже є — буде перезаписано лише вибрані поля.')
            ->modalSubmitActionLabel('Перекласти')
            ->form([
                Select::make('target_language_id')
                    ->label('Мова перекладу')
                    ->options(fn() => Language::query()
                        ->where('is_active', true)
                        ->where('is_default', false)
                        ->pluck('name', 'id'))
                    ->required(),

                CheckboxList::make('selected_fields')
                    ->label('Поля для перекладу')
                    ->options(
                        collect($fields)
                            ->mapWithKeys(fn($f) => [$f => self::$fieldLabels[$f] ?? $f])
                            ->toArray()
                    )
                    ->default($fields)
                    ->columns(3),
            ])
            ->action(function (Collection $records, array $data) use ($fields) {
                $service = app(DeepLTranslationService::class);

                if (!$service->isConfigured()) {
                    Notification::make()->danger()
                        ->title('DeepL не налаштовано')
                        ->body('Додайте DEEPL_API_KEY до .env файлу')
                        ->send();
                    return;
                }

                $fieldsToTranslate = !empty($data['selected_fields'])
                    ? $data['selected_fields']
                    : $fields;

                $sourceLang = Language::query()->where('is_default', true)->first();
                $targetLang = Language::find($data['target_language_id']);

                $translated = 0;
                $skipped    = 0;
                $errors     = 0;

                foreach ($records as $record) {
                    $record->loadMissing('translations');
                    $sourceTranslation = $record->translations->firstWhere('language_id', $sourceLang->id);

                    if (!$sourceTranslation || empty($sourceTranslation->name)) {
                        $skipped++;
                        continue;
                    }

                    try {
                        $texts = collect($fieldsToTranslate)
                            ->mapWithKeys(fn($f) => [$f => $sourceTranslation->$f ?? ''])
                            ->toArray();

                        $result = $service->translateBatch($texts, $targetLang->code, $sourceLang->code);

                        $record->translations()->updateOrCreate(
                            ['language_id' => $targetLang->id],
                            $result
                        );

                        $translated++;
                    } catch (\Exception $e) {
                        $errors++;
                    }
                }

                if ($errors > 0) {
                    Notification::make()->danger()
                        ->title("Помилки при перекладі: {$errors}")
                        ->send();
                }

                Notification::make()->success()
                    ->title("Перекладено на «{$targetLang->name}»: {$translated}")
                    ->body($skipped > 0 ? "Пропущено (немає uk тексту): {$skipped}" : null)
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    // --- Хелпери для Select опцій ---

    private static function getLanguageOptions(?Model $record, array $fields): array
    {
        if (!$record) {
            return Language::query()->where('is_active', true)
                ->where('is_default', false)->pluck('name', 'id')->toArray();
        }

        $record->loadMissing('translations');

        return Language::query()
            ->where('is_active', true)
            ->where('is_default', false)
            ->get()
            ->filter(fn($lang) => self::hasMissingFields($record, $lang->id, $fields))
            ->pluck('name', 'id')
            ->toArray();
    }

    private static function hasMissingFields(Model $record, int $langId, array $fields): bool
    {
        $translation = $record->translations->firstWhere('language_id', $langId);
        return collect($fields)->some(fn($f) => empty($translation?->$f ?? ''));
    }
}
