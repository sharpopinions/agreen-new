<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;

class UiTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [

            // ── АДМІНКА ──────────────────────────────────────────────────────
            // Поля форм
            ['group' => 'admin', 'key' => 'fields.name',        'uk' => 'Назва',              'en' => 'Name',             'pl' => 'Nazwa'],
            ['group' => 'admin', 'key' => 'fields.slug',        'uk' => 'Slug (URL)',          'en' => 'Slug (URL)',       'pl' => 'Slug (URL)'],
            ['group' => 'admin', 'key' => 'fields.description', 'uk' => 'Опис',               'en' => 'Description',      'pl' => 'Opis'],
            ['group' => 'admin', 'key' => 'fields.is_active',   'uk' => 'Активний',           'en' => 'Active',           'pl' => 'Aktywny'],
            ['group' => 'admin', 'key' => 'fields.sort_order',  'uk' => 'Порядок сортування', 'en' => 'Sort order',       'pl' => 'Kolejność'],
            ['group' => 'admin', 'key' => 'fields.logo',        'uk' => 'Логотип',            'en' => 'Logo',             'pl' => 'Logo'],
            ['group' => 'admin', 'key' => 'fields.color',       'uk' => 'Колір тексту',       'en' => 'Text color',       'pl' => 'Kolor tekstu'],
            ['group' => 'admin', 'key' => 'fields.bg_color',    'uk' => 'Колір фону',         'en' => 'Background color', 'pl' => 'Kolor tła'],
            ['group' => 'admin', 'key' => 'fields.code',        'uk' => 'Код',                'en' => 'Code',             'pl' => 'Kod'],
            ['group' => 'admin', 'key' => 'fields.symbol',      'uk' => 'Символ',             'en' => 'Symbol',           'pl' => 'Symbol'],
            ['group' => 'admin', 'key' => 'fields.is_default',  'uk' => 'За замовчуванням',   'en' => 'Default',          'pl' => 'Domyślny'],
            ['group' => 'admin', 'key' => 'fields.locale',      'uk' => 'Локаль',             'en' => 'Locale',           'pl' => 'Język'],

            // Секції
            ['group' => 'admin', 'key' => 'sections.main',         'uk' => 'Основне',      'en' => 'General',      'pl' => 'Ogólne'],
            ['group' => 'admin', 'key' => 'sections.media',        'uk' => 'Медіа',        'en' => 'Media',        'pl' => 'Media'],
            ['group' => 'admin', 'key' => 'sections.translations', 'uk' => 'Переклади',    'en' => 'Translations', 'pl' => 'Tłumaczenia'],
            ['group' => 'admin', 'key' => 'sections.settings',     'uk' => 'Налаштування', 'en' => 'Settings',     'pl' => 'Ustawienia'],

            // Назви ресурсів (меню)
            ['group' => 'admin', 'key' => 'resources.category',       'uk' => 'Категорія',         'en' => 'Category',       'pl' => 'Kategoria'],
            ['group' => 'admin', 'key' => 'resources.categories',     'uk' => 'Категорії',         'en' => 'Categories',     'pl' => 'Kategorie'],
            ['group' => 'admin', 'key' => 'resources.brand',          'uk' => 'Бренд',             'en' => 'Brand',          'pl' => 'Marka'],
            ['group' => 'admin', 'key' => 'resources.brands',         'uk' => 'Бренди',            'en' => 'Brands',         'pl' => 'Marki'],
            ['group' => 'admin', 'key' => 'resources.badge',          'uk' => 'Бейдж',             'en' => 'Badge',          'pl' => 'Etykieta'],
            ['group' => 'admin', 'key' => 'resources.badges',         'uk' => 'Бейджі',            'en' => 'Badges',         'pl' => 'Etykiety'],
            ['group' => 'admin', 'key' => 'resources.product',        'uk' => 'Товар',             'en' => 'Product',        'pl' => 'Produkt'],
            ['group' => 'admin', 'key' => 'resources.products',       'uk' => 'Товари',            'en' => 'Products',       'pl' => 'Produkty'],
            ['group' => 'admin', 'key' => 'resources.stock_status',   'uk' => 'Статус наявності',  'en' => 'Stock status',   'pl' => 'Status dostępności'],
            ['group' => 'admin', 'key' => 'resources.stock_statuses', 'uk' => 'Статуси наявності', 'en' => 'Stock statuses', 'pl' => 'Statusy dostępności'],
            ['group' => 'admin', 'key' => 'resources.language',       'uk' => 'Мова',              'en' => 'Language',       'pl' => 'Język'],
            ['group' => 'admin', 'key' => 'resources.languages',      'uk' => 'Мови',              'en' => 'Languages',      'pl' => 'Języki'],
            ['group' => 'admin', 'key' => 'resources.currency',       'uk' => 'Валюта',            'en' => 'Currency',       'pl' => 'Waluta'],
            ['group' => 'admin', 'key' => 'resources.currencies',     'uk' => 'Валюти',            'en' => 'Currencies',     'pl' => 'Waluty'],

            // Групи навігації
            ['group' => 'admin', 'key' => 'groups.catalog',  'uk' => 'Каталог',      'en' => 'Catalog',  'pl' => 'Katalog'],
            ['group' => 'admin', 'key' => 'groups.settings', 'uk' => 'Налаштування', 'en' => 'Settings', 'pl' => 'Ustawienia'],

            // ── ФРОНТЕНД ─────────────────────────────────────────────────────
            // Навігація
            ['group' => 'frontend', 'key' => 'nav.home',     'uk' => 'Головна',  'en' => 'Home',    'pl' => 'Strona główna'],
            ['group' => 'frontend', 'key' => 'nav.catalog',  'uk' => 'Каталог',  'en' => 'Catalog', 'pl' => 'Katalog'],
            ['group' => 'frontend', 'key' => 'nav.brands',   'uk' => 'Бренди',   'en' => 'Brands',  'pl' => 'Marki'],
            ['group' => 'frontend', 'key' => 'nav.about',    'uk' => 'Про нас',  'en' => 'About',   'pl' => 'O nas'],
            ['group' => 'frontend', 'key' => 'nav.contacts', 'uk' => 'Контакти', 'en' => 'Contacts','pl' => 'Kontakty'],
            ['group' => 'frontend', 'key' => 'nav.blog',     'uk' => 'Блог',     'en' => 'Blog',    'pl' => 'Blog'],
            ['group' => 'frontend', 'key' => 'nav.cart',     'uk' => 'Кошик',    'en' => 'Cart',    'pl' => 'Koszyk'],
            ['group' => 'frontend', 'key' => 'nav.account',  'uk' => 'Кабінет',  'en' => 'Account', 'pl' => 'Konto'],
            ['group' => 'frontend', 'key' => 'nav.login',    'uk' => 'Увійти',   'en' => 'Log in',  'pl' => 'Zaloguj się'],
            ['group' => 'frontend', 'key' => 'nav.logout',   'uk' => 'Вийти',    'en' => 'Log out', 'pl' => 'Wyloguj się'],

            // Картка товару / сторінка товару
            ['group' => 'frontend', 'key' => 'product.add_to_cart',     'uk' => 'Додати в кошик',      'en' => 'Add to cart',       'pl' => 'Dodaj do koszyka'],
            ['group' => 'frontend', 'key' => 'product.buy_now',         'uk' => 'Купити зараз',        'en' => 'Buy now',           'pl' => 'Kup teraz'],
            ['group' => 'frontend', 'key' => 'product.preorder',        'uk' => 'Передзамовлення',     'en' => 'Pre-order',         'pl' => 'Zamów w przedsprzedaży'],
            ['group' => 'frontend', 'key' => 'product.in_stock',        'uk' => 'В наявності',         'en' => 'In stock',          'pl' => 'Dostępny'],
            ['group' => 'frontend', 'key' => 'product.out_of_stock',    'uk' => 'Немає в наявності',   'en' => 'Out of stock',      'pl' => 'Niedostępny'],
            ['group' => 'frontend', 'key' => 'product.sku',             'uk' => 'Артикул',             'en' => 'SKU',               'pl' => 'Numer katalogowy'],
            ['group' => 'frontend', 'key' => 'product.brand',           'uk' => 'Бренд',               'en' => 'Brand',             'pl' => 'Marka'],
            ['group' => 'frontend', 'key' => 'product.description',     'uk' => 'Опис',                'en' => 'Description',       'pl' => 'Opis'],
            ['group' => 'frontend', 'key' => 'product.characteristics', 'uk' => 'Характеристики',      'en' => 'Specifications',    'pl' => 'Specyfikacja'],
            ['group' => 'frontend', 'key' => 'product.reviews',         'uk' => 'Відгуки',             'en' => 'Reviews',           'pl' => 'Opinie'],
            ['group' => 'frontend', 'key' => 'product.recently_viewed', 'uk' => 'Нещодавно переглянуті','en' => 'Recently viewed',  'pl' => 'Ostatnio oglądane'],
            ['group' => 'frontend', 'key' => 'product.related',         'uk' => 'Схожі товари',        'en' => 'Related products',  'pl' => 'Podobne produkty'],

            // Кошик
            ['group' => 'frontend', 'key' => 'cart.title',        'uk' => 'Кошик',             'en' => 'Cart',           'pl' => 'Koszyk'],
            ['group' => 'frontend', 'key' => 'cart.empty',        'uk' => 'Кошик порожній',    'en' => 'Cart is empty',  'pl' => 'Koszyk jest pusty'],
            ['group' => 'frontend', 'key' => 'cart.total',        'uk' => 'Разом',             'en' => 'Total',          'pl' => 'Razem'],
            ['group' => 'frontend', 'key' => 'cart.checkout',     'uk' => 'Оформити замовлення','en' => 'Checkout',      'pl' => 'Zamów'],
            ['group' => 'frontend', 'key' => 'cart.quantity',     'uk' => 'Кількість',         'en' => 'Quantity',       'pl' => 'Ilość'],
            ['group' => 'frontend', 'key' => 'cart.remove',       'uk' => 'Видалити',          'en' => 'Remove',         'pl' => 'Usuń'],
            ['group' => 'frontend', 'key' => 'cart.continue',     'uk' => 'Продовжити покупки','en' => 'Continue shopping','pl' => 'Kontynuuj zakupy'],

            // Загальні елементи UI
            ['group' => 'frontend', 'key' => 'ui.search',         'uk' => 'Пошук',             'en' => 'Search',         'pl' => 'Szukaj'],
            ['group' => 'frontend', 'key' => 'ui.search_placeholder','uk' => 'Пошук товарів...','en' => 'Search products...','pl' => 'Szukaj produktów...'],
            ['group' => 'frontend', 'key' => 'ui.all',            'uk' => 'Всі',               'en' => 'All',            'pl' => 'Wszystkie'],
            ['group' => 'frontend', 'key' => 'ui.show_more',      'uk' => 'Показати більше',   'en' => 'Show more',      'pl' => 'Pokaż więcej'],
            ['group' => 'frontend', 'key' => 'ui.loading',        'uk' => 'Завантаження...',   'en' => 'Loading...',     'pl' => 'Ładowanie...'],
            ['group' => 'frontend', 'key' => 'ui.filter',         'uk' => 'Фільтр',            'en' => 'Filter',         'pl' => 'Filtruj'],
            ['group' => 'frontend', 'key' => 'ui.sort',           'uk' => 'Сортування',        'en' => 'Sort',           'pl' => 'Sortuj'],
            ['group' => 'frontend', 'key' => 'ui.price_from',     'uk' => 'Ціна від',          'en' => 'Price from',     'pl' => 'Cena od'],
            ['group' => 'frontend', 'key' => 'ui.price_to',       'uk' => 'до',                'en' => 'to',             'pl' => 'do'],
            ['group' => 'frontend', 'key' => 'ui.currency_uah',   'uk' => 'грн',               'en' => 'UAH',            'pl' => 'UAH'],
        ];

        foreach ($translations as $item) {
            $record = UiTranslation::updateOrCreate(
                ['store_id' => 1, 'key' => $item['key'], 'group' => $item['group']],
                []
            );

            foreach (['uk', 'en', 'pl'] as $locale) {
                if (!empty($item[$locale])) {
                    $record->values()->updateOrCreate(
                        ['locale' => $locale],
                        ['value' => $item[$locale]]
                    );
                }
            }
        }

        $this->command->info('✓ Системні переклади: ' . count($translations) . ' ключів (admin + frontend)');
    }
}
