<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/** Безпечний HTML описів з адмінки (RichEditor) для v-html на сайті. */
class Html
{
    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        // Старі описи — звичайний текст: абзаци з порожніх рядків
        if ($html === strip_tags($html)) {
            return collect(preg_split('/\R{2,}/u', $html))
                ->map(fn($p) => '<p>' . nl2br(e(trim($p)), false) . '</p>')
                ->implode('');
        }

        static $sanitizer;
        $sanitizer ??= new HtmlSanitizer((new HtmlSanitizerConfig())
            ->allowElement('p')->allowElement('br')->allowElement('strong')->allowElement('b')
            ->allowElement('em')->allowElement('i')->allowElement('u')->allowElement('s')
            ->allowElement('h2')->allowElement('h3')->allowElement('h4')
            ->allowElement('ul')->allowElement('ol')->allowElement('li')->allowElement('blockquote')
            ->allowElement('a', ['href', 'title'])
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->allowRelativeLinks()
            ->forceAttribute('a', 'rel', 'noopener'));

        return $sanitizer->sanitize($html);
    }
}
