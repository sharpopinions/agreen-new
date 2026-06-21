<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DeepLTranslationService
{
    private string $apiKey;
    private string $apiUrl;

    // Маппінг наших кодів → DeepL коди
    private array $langMap = [
        'uk' => 'UK',
        'en' => 'EN',
        'pl' => 'PL',
        'de' => 'DE',
        'fr' => 'FR',
        'es' => 'ES',
        'it' => 'IT',
        'cs' => 'CS',
        'ro' => 'RO',
        'hu' => 'HU',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.deepl.api_key', '');
        $this->apiUrl = config('services.deepl.free', true)
            ? 'https://api-free.deepl.com/v2/translate'
            : 'https://api.deepl.com/v2/translate';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Перекладає масив рядків за один запит до API.
     * Ключі масиву зберігаються (порожні рядки пропускаються).
     *
     * @param  array<string, string|null>  $texts  ['name' => 'Категорія', 'description' => '...']
     * @return array<string, string>
     */
    public function translateBatch(array $texts, string $targetCode, string $sourceCode): array
    {
        $nonEmpty = array_filter(
            $texts,
            fn($v) => is_string($v) && trim($v) !== ''
        );

        if (empty($nonEmpty)) {
            return $texts;
        }

        $response = Http::withHeaders([
            'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
        ])->post($this->apiUrl, [
            'text'        => array_values($nonEmpty),
            'source_lang' => $this->toDeepL($sourceCode),
            'target_lang' => $this->toDeepL($targetCode),
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                'DeepL API error ' . $response->status() . ': ' . $response->body()
            );
        }

        $translated = collect($response->json('translations'))
            ->pluck('text')
            ->values();

        $result = $texts;
        $i = 0;
        foreach ($texts as $key => $value) {
            if (is_string($value) && trim($value) !== '') {
                $result[$key] = $translated[$i++] ?? $value;
            }
        }

        return $result;
    }

    private function toDeepL(string $code): string
    {
        return $this->langMap[strtolower($code)] ?? strtoupper($code);
    }
}
