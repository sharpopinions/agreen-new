# Схема бази даних

Повна схема з 28 таблиць. Порядок відповідає порядку міграцій.

---

## SaaS шар

### stores
```
id, name, domain (unique), settings (json), plan, is_active, timestamps
```

### store_domains
```
id, store_id (FK), domain (unique), is_primary, timestamps
```

---

## Мова і валюта

### languages
```
id, store_id (FK), code(5), name, is_default, is_active, timestamps
```

### currencies
```
id, store_id (FK), code(3), name, symbol, rate(10,4), is_default, is_active, timestamps
```

---

## Каталог

### stock_statuses
```
id, store_id (FK), key, is_in_stock, sort_order, is_active, timestamps
```
Значення: in_stock, preorder, in_transit, expected

### stock_status_translations
```
id, stock_status_id (FK), locale, name
UNIQUE(stock_status_id, locale)
```

### categories
```
id, store_id (FK), parent_id (FK nullable → self), image, og_image, sort_order, is_active, timestamps
```

### category_translations
```
id, category_id (FK), locale, name, slug, short_description, description
meta_title, meta_h1, meta_description, meta_keywords, og_title, og_description
UNIQUE(category_id, locale)
INDEX(locale, slug)
```

### brands
```
id, store_id (FK), logo, og_image, sort_order, is_active, timestamps
```

### brand_translations
```
id, brand_id (FK), locale, name, slug, description
meta_title, meta_description, og_title, og_description
UNIQUE(brand_id, locale)
```

### badges
```
id, store_id (FK), color (nullable), sort_order, is_active, timestamps
```

### badge_translations
```
id, badge_id (FK), locale, name
UNIQUE(badge_id, locale)
```

### products
```
id, store_id (FK), brand_id (FK nullable nullOnDelete), badge_id (FK nullable nullOnDelete)
stock_status_id (FK nullable nullOnDelete), currency_id (FK nullable nullOnDelete)
replaced_by_id (FK nullable → self nullOnDelete)
sku (unique), price(15,2), old_price(15,2 nullable)
preorder_days (nullable), image (nullable), og_image (nullable)
sort_order, is_active, timestamps
```

### product_translations
```
id, product_id (FK), locale, name, slug
short_description, description, warning_text
meta_title, meta_h1, meta_description, meta_keywords, og_title, og_description
UNIQUE(product_id, locale)
UNIQUE(locale, slug)
```

### product_images
```
id, product_id (FK cascadeOnDelete), path, sort_order, is_main, timestamps
```

### category_product (pivot)
```
category_id (FK cascadeOnDelete), product_id (FK cascadeOnDelete)
PRIMARY KEY(category_id, product_id)
```

---

## Атрибути і фільтри (EAV)

### attribute_definitions
```
id, store_id (FK), type (enum: select,number,color,boolean,text)
is_filterable, is_comparable, sort_order, is_active, timestamps
```

### attribute_definition_translations
```
id, attribute_definition_id (FK), locale, name
UNIQUE(attribute_definition_id, locale)
```

### attribute_values
```
id, attribute_definition_id (FK cascadeOnDelete), sort_order, timestamps
```

### attribute_value_translations
```
id, attribute_value_id (FK), locale, name, value (nullable — для кольору hex)
UNIQUE(attribute_value_id, locale)
```

### product_attribute_values (pivot)
```
product_id (FK cascadeOnDelete), attribute_value_id (FK cascadeOnDelete)
PRIMARY KEY(product_id, attribute_value_id)
```

### category_attribute_definitions (pivot)
```
category_id (FK cascadeOnDelete), attribute_definition_id (FK cascadeOnDelete)
display_type (enum: checkbox,range,color_swatch,boolean), sort_order
PRIMARY KEY(category_id, attribute_definition_id)
```

---

## Інтеграції

### integrations
```
id, store_id (FK), name, driver (one_c|sap|excel|api|crm)
settings (json), is_active, timestamps
```

### integration_sync_logs
```
id, integration_id (FK cascadeOnDelete), entity_type, status (success|error|partial)
records_processed, error_message (nullable), created_at
```

---

## Доставка і оплата

### shipping_providers
```
id, store_id (FK), driver (nova_poshta|ukrposhta|meest|justin|courier|self_pickup)
settings (json), sort_order, is_active, timestamps
```

### shipping_provider_translations
```
id, shipping_provider_id (FK), locale, name
UNIQUE(shipping_provider_id, locale)
```

### payment_providers
```
id, store_id (FK), driver (liqpay|wayforpay|card|invoice_vat|invoice_no_vat|postpay|deferred)
settings (json), sort_order, is_active, timestamps
```

### payment_provider_translations
```
id, payment_provider_id (FK), locale, name
UNIQUE(payment_provider_id, locale)
```

---

## Порядок міграцій

```
1.  stores
2.  store_domains
3.  languages
4.  currencies
5.  stock_statuses
6.  stock_status_translations
7.  categories
8.  category_translations
9.  brands
10. brand_translations
11. badges
12. badge_translations
13. products
14. product_translations
15. product_images
16. category_product
17. attribute_definitions
18. attribute_definition_translations
19. attribute_values
20. attribute_value_translations
21. product_attribute_values
22. category_attribute_definitions
23. integrations
24. integration_sync_logs
25. shipping_providers
26. shipping_provider_translations
27. payment_providers
28. payment_provider_translations
```
