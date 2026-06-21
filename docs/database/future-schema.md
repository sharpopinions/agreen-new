# Схема БД — майбутні фази (Phase 12–19)

Таблиці каталогу описані в `schema.md`. Тут — всі інші таблиці з ТЗ.

---

## Phase 12 — Авторизація + Кабінет клієнта

### users (вже є — Laravel default, розширюємо)
```
+ store_id (FK)
+ role: client | business_client | business_partner | employee
+ phone
+ avatar
+ locale (uk | en | pl)
+ theme (light | dark)
+ admin_locale
```

### companies
Юридичні особи бізнес-клієнтів і партнерів.
```
id, store_id (FK), owner_id (FK → users)
name, edrpou, type (tov | fop | individual)
manager_id (FK → users nullable) ← закріплений менеджер A-green
contract_status (pending | active | expired)
is_active, timestamps
```

### company_employees
Співробітники компанії з ролями і правами.
```
id, company_id (FK), user_id (FK)
role: admin | brand_manager | logist | accountant | user
permissions (json) ← чекбокси доступів
is_active, timestamps
UNIQUE(company_id, user_id)
```

### company_addresses
Адреси доставки компанії (кілька).
```
id, company_id (FK), label, recipient_name
street, city, region, postal_code, country
shipping_provider_id (FK nullable) ← прив'язаний перевізник
is_default, timestamps
```

### support_tickets
Запити в технічну підтримку.
```
id, store_id (FK), user_id (FK), company_id (FK nullable)
type: consultation | service | specialist_visit
status: new | in_progress | waiting | done | closed
subject, created_at, updated_at
```

### support_ticket_messages
```
id, ticket_id (FK cascadeOnDelete), user_id (FK)
message (text), created_at
```

### wishlists
Список улюблених товарів.
```
id, user_id (FK cascadeOnDelete), product_id (FK cascadeOnDelete)
created_at
UNIQUE(user_id, product_id)
```

### comparisons
Список порівняння (до 4 товарів).
```
id, user_id (FK cascadeOnDelete), product_id (FK cascadeOnDelete)
created_at
UNIQUE(user_id, product_id)
```

---

## Phase 13 — Кошик + Замовлення

### order_statuses
```
id, store_id (FK), key, color, sort_order, is_active, timestamps
```
Значення: pending | confirmed | awaiting_payment | processing | shipped | delivered | completed | cancelled

### order_status_translations
```
id, order_status_id (FK), locale, name
UNIQUE(order_status_id, locale)
```

### orders
```
id, store_id (FK), user_id (FK nullable), company_id (FK nullable)
order_status_id (FK), shipping_provider_id (FK nullable)
payment_provider_id (FK nullable)
type: standard | preorder | dropship
number (unique) ← #A-2026-00001
total (decimal 15,2), shipping_cost (decimal 10,2)
shipping_address (json) ← знімок адреси на момент замовлення
payment_status: pending | paid | failed | refunded
notes (text nullable)
editable_until (timestamp nullable) ← бізнес-клієнт може редагувати 1 год
tracking_number (string nullable)
timestamps
```

### order_items
```
id, order_id (FK cascadeOnDelete), product_id (FK nullable nullOnDelete)
name (string) ← знімок назви на момент замовлення
sku (string)
price (decimal 15,2), quantity (integer), total (decimal 15,2)
```

---

## Phase 14 — Бізнес-клієнт

### b2b_prices
Персональні ціни з ERP (1С або інша система).
```
id, store_id (FK), product_id (FK cascadeOnDelete)
company_id (FK cascadeOnDelete)
price (decimal 15,2)
valid_from (date nullable), valid_to (date nullable)
source: erp | manual ← звідки прийшла ціна
timestamps
UNIQUE(product_id, company_id)
```

### contracts
Договори компаній.
```
id, store_id (FK), company_id (FK)
number (string), file (string nullable)
status: pending | signed | expired
signed_at (date nullable), expires_at (date nullable)
timestamps
```

### documents
Рахунки, акти, накладні — прив'язані до замовлень.
```
id, store_id (FK), company_id (FK nullable), order_id (FK nullable)
type: invoice_vat | invoice_no_vat | act | waybill | certificate
number (string), file (string nullable)
amount (decimal 15,2 nullable)
issued_at (date), timestamps
```

### cashback_transactions
Cashback баланс і операції.
```
id, store_id (FK), user_id (FK), company_id (FK nullable)
order_id (FK nullable)
type: earn | spend
amount (decimal 10,2)
balance_after (decimal 10,2)
description (string nullable)
created_at
```

---

## Phase 16 — Бізнес-партнер + Дропшипінг

### dropship_settings
Налаштування дропшипінгу per компанія.
```
id, company_id (FK unique)
sender_name, sender_phone
warehouse_ref (string) ← реф складу НП
template_type: with_prices | without_prices
is_active, timestamps
```

### dropship_orders
Дропшип замовлення (окремий трекінг).
```
id, order_id (FK cascadeOnDelete)
recipient_name, recipient_phone
recipient_city, recipient_warehouse
tracking_number (nullable)
status: pending | sent | delivered | returned
timestamps
```

---

## Phase 17 — Контент

### post_categories
Категорії блогу (Новини / Події / ЗМІ про нас).
```
id, store_id (FK), slug (unique per store), sort_order, is_active, timestamps
```

### post_category_translations
```
id, post_category_id (FK), locale, name
UNIQUE(post_category_id, locale)
```

### posts
Статті блогу / новини / події.
```
id, store_id (FK), author_id (FK → users nullable)
post_category_id (FK nullable nullOnDelete)
image (nullable), og_image (nullable)
is_published (boolean), published_at (timestamp nullable)
has_registration (boolean) ← для подій — форма реєстрації
sort_order, timestamps
```

### post_translations
```
id, post_id (FK), locale
title, slug, excerpt (text nullable), content (longtext nullable)
meta_title, meta_h1, meta_description, meta_keywords
og_title, og_description
UNIQUE(post_id, locale)
UNIQUE(locale, slug)
```

### promotions
Акційні пропозиції.
```
id, store_id (FK), image (nullable)
discount_percent (decimal 5,2 nullable)
starts_at (timestamp), ends_at (timestamp)
is_active, sort_order, timestamps
```

### promotion_translations
```
id, promotion_id (FK), locale
name, slug, description (text nullable)
meta_title, meta_description, og_title, og_description
UNIQUE(promotion_id, locale)
UNIQUE(locale, slug)
```

### promotion_products (pivot)
Які товари входять в акцію.
```
promotion_id (FK cascadeOnDelete), product_id (FK cascadeOnDelete)
PRIMARY KEY(promotion_id, product_id)
```

### services
Послуги компанії (Навчальний центр, Технічна підтримка, Проектування).
```
id, store_id (FK), icon (nullable), image (nullable)
sort_order, is_active, timestamps
```

### service_translations
```
id, service_id (FK), locale
name, slug, short_description (text nullable)
description (longtext nullable)
meta_title, meta_description, og_title, og_description
UNIQUE(service_id, locale)
UNIQUE(locale, slug)
```

### vacancies
Вакансії.
```
id, store_id (FK), sort_order, is_active, timestamps
```

### vacancy_translations
```
id, vacancy_id (FK), locale
title, description (longtext), requirements (text nullable)
conditions (text nullable)
UNIQUE(vacancy_id, locale)
```

### testimonials
Відгуки клієнтів (для сторінки Послуги / Партнерам).
```
id, store_id (FK), author_name, company (nullable)
rating (tinyint 1-5), avatar (nullable)
sort_order, is_active, timestamps
```

### testimonial_translations
```
id, testimonial_id (FK), locale, content (text)
UNIQUE(testimonial_id, locale)
```

### sliders
Банери / слайдери (головна сторінка та ін.).
```
id, store_id (FK), image, link (nullable)
sort_order, is_active, timestamps
```

### slider_translations
```
id, slider_id (FK), locale
title (nullable), subtitle (nullable), button_text (nullable)
UNIQUE(slider_id, locale)
```

---

## Повна картина — всі таблиці проекту

### Phase 7 (каталог) — 28 таблиць
Описані в `schema.md`

### Phase 12-17 (решта) — ~30 таблиць
```
users (розширений), companies, company_employees, company_addresses
support_tickets, support_ticket_messages
wishlists, comparisons
order_statuses + translations, orders, order_items
b2b_prices, contracts, documents, cashback_transactions
dropship_settings, dropship_orders
post_categories + translations, posts + translations
promotions + translations, promotion_products
services + translations
vacancies + translations
testimonials + translations
sliders + translations
```

**Разом по проекту: ~58 таблиць**
