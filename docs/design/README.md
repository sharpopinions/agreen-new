# Дизайн A-green

Джерела:
- **Figma:** [A-green](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green), один робочий аркуш «Page 1», 88 фреймів (екрани 1440px і кілька окремих компонентів).
- **Таблиця ТЗ:** [ТЗ по сайту Агрін](https://docs.google.com/spreadsheets/d/17ZqtYuUHBDx-NYJZpcxO-u-ZDARXwo_qfwZFrTFWKYA/edit), 35 сторінок, у всіх статус «Готово / Погоджено».

Макет є прототипом: сірі прямокутники `#D9D9D9` означають місця під фото, а не фінальні зображення.

## Токени

Токени задані в `resources/scss/abstracts/_variables.scss`. Значення нижче взято з макета за частотою використання.

### Кольори

| Токен | Значення | Де в макеті |
|---|---|---|
| `--color-primary` | `#1A5CB9` | кнопки (заливка), посилання «Дивитися всі →», «Детальніше», активні стани |
| `--color-text` | `#000000` | основний текст, іконки |
| `--color-text-2` | `rgba(0,0,0,.5)` | артикул, залишок, дати, рейтинг |
| `--color-text-3` | `rgba(0,0,0,.3)` | неактивні зірки, плейсхолдери |
| `--color-text-label` | `#838383` | підписи полів форм |
| `--color-border` | `#000000` | рамки 1px у карток, інпутів і кнопок-обведень |
| `--color-border-2` | `rgba(0,0,0,.2)` | роздільники, другорядні лінії |
| `--color-bg-alt` / `--color-muted` | `#EEEEEE` | фон блоків і секцій |
| `--color-placeholder` | `#D9D9D9` | плейсхолдери зображень, активний пункт меню кабінету |
| `--color-success` | `#487E01` | «Є в наявності» |
| `--color-warn` / `--color-accent-gold` | `#C69500` | «Під замовлення» |

`--color-primary-hover` (`#154A94`) у макеті відсутній, це похідне значення.

### Типографіка

Шрифт **Inter** підключається локально через пакет `@fontsource/inter` у `resources/js/app.js`. Накреслення: Regular (400), Medium (500), Light (300), Semi Bold (600). Висота рядка в макеті переважно 100%.

| Токен | px | Використання |
|---|---|---|
| `--font-size-xs` | 10 | дрібні мітки |
| `--font-size-sm` | 12 | метадані, найчастіший розмір |
| `--font-size-base` | 14 | текст карток, кнопки |
| `--font-size-lg` | 16 | основний текст |
| `--font-size-xl` | 18 | підзаголовки |
| `--font-size-2xl` | 20 | заголовки карток |
| `--font-size-3xl` | 24 | заголовки блоків |
| `--font-size-4xl` | 28 | заголовки секцій |
| `--font-size-5xl` | 32 | заголовки сторінок |
| `--font-size-display` | 46 | герой на головній |

Заголовки в макеті мають вагу Medium (500).

### Форма та сітка

- **Кути прямі.** `--radius-sm/md/lg = 0`. Заокруглення 20px мають лише «пілюлі» (теги, лічильники), для них `--radius-full`.
- **Рамки** всюди 1px.
- **Кнопка:** висота 40px, заливка `--color-primary`, білий текст.
- **Тіні:** майже відсутні. Є одна м'яка тінь `--shadow-md` для поп-апів.
- **Контейнер:** 1440px, бічні поля 60px (`--container-gutter`), контент 1320px.

## Сторінки → екрани Figma → ТЗ

| № | Сторінка | Екран у Figma | ТЗ |
|---|---|---|---|
| 1 | Головна | [`Main`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=290-716) | [ТЗ](https://docs.google.com/document/d/17RQdTDUCx5An_dYTSFMVZ2yNbExpW-EBJGA7B8o3GKM/edit?tab=t.0) |
| 2 | Про компанію | [`About`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=571-1503) | — |
| 3 | Каталог загальна | [`Catalog`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=410-772) | [ТЗ](https://docs.google.com/document/d/1z4pAt7vwnn3TsNkf2ei3ZI5I4rdjh17GLpp5Q8JcWts/edit?tab=t.0) |
| 4 | Каталог усі товари | [`Catalog//All-products`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=782-2685) | [ТЗ](https://docs.google.com/document/d/1z4pAt7vwnn3TsNkf2ei3ZI5I4rdjh17GLpp5Q8JcWts/edit?tab=t.1) |
| 5 | Каталог з обраною категорією | [`Catalog-category`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=533-15083) | [ТЗ](https://docs.google.com/document/d/1z4pAt7vwnn3TsNkf2ei3ZI5I4rdjh17GLpp5Q8JcWts/edit?tab=t.2) |
| 6 | Каталог з обраною підкатегорією | [`Catalog//Subcategory`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=533-15831) | [ТЗ](https://docs.google.com/document/d/1z4pAt7vwnn3TsNkf2ei3ZI5I4rdjh17GLpp5Q8JcWts/edit?tab=t.2) |
| 7 | Сторінка продукту через каталог | [`Product-card`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=441-938) | [ТЗ](https://docs.google.com/document/d/1z4pAt7vwnn3TsNkf2ei3ZI5I4rdjh17GLpp5Q8JcWts/edit?tab=t.0) |
| 8 | Бренди | [`Brands`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=685-1557) | [ТЗ](https://docs.google.com/document/d/1_SQKw6p0JBflEMq-ltYb3cYpN07S_VQ4nfJ31WhhrWc/edit) |
| 9 | Продукція бренду | [`Brand//Products`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=688-2590) | [ТЗ](https://docs.google.com/document/d/1_SQKw6p0JBflEMq-ltYb3cYpN07S_VQ4nfJ31WhhrWc/edit) |
| 10 | Про бренд | [`Brand//About`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=696-3321) | [ТЗ](https://docs.google.com/document/d/1_SQKw6p0JBflEMq-ltYb3cYpN07S_VQ4nfJ31WhhrWc/edit) |
| 11 | Медіабанк бренду | [`Brand//Media`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=696-4017) | [ТЗ](https://docs.google.com/document/d/1_SQKw6p0JBflEMq-ltYb3cYpN07S_VQ4nfJ31WhhrWc/edit) |
| 12 | Сторінка продукту через бренд | [`Product-card//brands`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=739-2215) | [ТЗ](https://docs.google.com/document/d/1_SQKw6p0JBflEMq-ltYb3cYpN07S_VQ4nfJ31WhhrWc/edit) |
| 13 | Порівняння товарів | [`Compare//Dispenser`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=839-3449) | [ТЗ](https://docs.google.com/document/d/1jiA05tQUEA6AegH7Aqza2HFEUnQ2ykqdehWf0Gcjg5k/edit?tab=t.0) |
| 14 | Політика конфіденційності та файли кукі | [`Privacy-Policy`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=778-2498) | [ТЗ](https://docs.google.com/document/d/1ifAHCWZHuskH7sKZrwLmVMloBnPN4gLrFogg4M4NFCQ/edit) |
| 15 | Кошик | [`Cart`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=902-7030) | [ТЗ](https://docs.google.com/document/d/147NrGLhpHTILA1X32LIXF-B7mWXhkZPZF-BV9kpXdyQ/edit) |
| 16 | Кошик — оформлення замовлення | [`Cart//Checkout-1`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=902-7255) | [ТЗ](https://docs.google.com/document/d/147NrGLhpHTILA1X32LIXF-B7mWXhkZPZF-BV9kpXdyQ/edit) |
| 17 | Послуги | [`Services`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=986-7192) | [ТЗ](https://docs.google.com/document/d/1msgnOF-TpHqDtwrDMNsvUGtZWiej56JtlbE2elGTu5Y/edit?tab=t.0) |
| 18 | Послуга | [`Services//Study-center`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=986-7451) | [ТЗ](https://docs.google.com/document/d/1msgnOF-TpHqDtwrDMNsvUGtZWiej56JtlbE2elGTu5Y/edit?tab=t.0) |
| 19 | Партнерам | [`Partners`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=944-9812) | [ТЗ](https://docs.google.com/document/d/1z6T2CvmE97znbhAbw0KcUMjoAKpEmIkkKga4FrrysJk/edit) |
| 20 | Блог | [`Blog`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=914-5239) | [ТЗ](https://docs.google.com/document/d/1nJWDxwf7gi1bhG5-hK-TetQvVqqHqmo_JOm1zWbeYKw/edit?tab=t.0) |
| 21 | Новина | [`Blog//Post`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=914-7012) | [ТЗ](https://docs.google.com/document/d/1nJWDxwf7gi1bhG5-hK-TetQvVqqHqmo_JOm1zWbeYKw/edit?tab=t.0) |
| 22 | Подія | [`Blog//Event`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=914-7175) | [ТЗ](https://docs.google.com/document/d/1nJWDxwf7gi1bhG5-hK-TetQvVqqHqmo_JOm1zWbeYKw/edit?tab=t.0) |
| 23 | Контакти | [`Contacts`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=820-3375) | [ТЗ](https://docs.google.com/document/d/1vVHXiJFpHNQwTxFk9RmBI7VbLnl9132PavTLCgUZN9Y/edit?tab=t.0) |
| 24 | 404 | [`404`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=944-10035) | [ТЗ](https://docs.google.com/document/d/1CCDq1z3mqVcAeGnnveoQO0i4-33A40KtOjoOzkL1d5o/edit?tab=t.0) |
| 25 | Доставка та оплата | [`Delivery`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=774-2289) | [ТЗ](https://docs.google.com/document/d/1_SXRURTlCow32FOwhwTyYv3jwBTGX0DrrZAnr48V5uo/edit?tab=t.0) |
| 26 | Договір публічної оферти | [`Public-offer`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=870-3671) | [ТЗ](https://docs.google.com/document/d/19pYpq-r2UCKKZOhcol3-7vBV-B8KDPnYkHWPESh5srw/edit?tab=t.0) |
| 27 | Вакансії | [`Vacancies`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=967-6973) | [ТЗ](https://docs.google.com/document/d/1MwcR-zBmvNslD01G-4uY_WDTKmVoSV56B0VvLTWWvGc/edit?tab=t.0) |
| 28 | Акції | [`Aktsyii`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=1041-9596) | [ТЗ](https://docs.google.com/document/d/1x0Jd-uGdBRId-xTiyoxpBq96COPgv-XfnowNpGMUIdA/edit?tab=t.0) |
| 29 | Акція | [`Aktsyia`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=1046-7737) | [ТЗ](https://docs.google.com/document/d/1x0Jd-uGdBRId-xTiyoxpBq96COPgv-XfnowNpGMUIdA/edit?tab=t.0) |
| 30 | Подяка за звернення | [`Thank//Form-submit`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=967-7237) | [ТЗ](https://docs.google.com/document/d/1usyExb32kohsbIr8DtBbX356saoevr-BZnSNecMCieE/edit?tab=t.0) |
| 31 | Подяка за замовлення | [`Thank//Order`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=986-7893) | [ТЗ](https://docs.google.com/document/d/1usyExb32kohsbIr8DtBbX356saoevr-BZnSNecMCieE/edit?tab=t.0) |
| 32 | Повернення та обмін | [`Return `](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=1041-7484) | [ТЗ](https://docs.google.com/document/d/1xp50cxm-ue0TZX0O4cl36ZtSqPONRyKzzWSSDnIr1ig/edit?tab=t.0) |
| 33 | Кабінет зареєстрованого клієнта | [`client-dashboard//main-catalog`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=935-5924) | [ТЗ](https://docs.google.com/document/d/147POHX4Wr1m8GbbALHVG_ji7sEav34D_skkDmZW2t1g/edit?tab=t.0) |
| 34 | Кабінет бізнес-клієнта | [`business-client-dashboard//main-catalog`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=935-5672) | [ТЗ](https://docs.google.com/document/d/1VqzGh8QBfM7k5En-37DAsTM_sSHk44k3e_ok2f-5GsA/edit?tab=t.0) |
| 35 | Кабінет бізнес-партнера | [`business-partner-dashboard//main-catalog`](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green?node-id=937-6355) | [ТЗ](https://docs.google.com/document/d/1WdOt9BXdi-i_n87_LOsL2RQdEk5B2-wRQQ6sB4hBAA8/edit?tab=t.0) |

Для «Про компанію» в таблиці немає посилання на ТЗ.

### Інші екрани (стани, поп-апи, кроки)

- `business-partner-login` — `4:2`
- `business-partner-registration` — `18:112`
- `business-partner-registration-confirmation` — `18:186`
- `business-partner-role-selection` — `18:297`
- `business-partner-role-selection-2` — `18:402`
- `business-partner-role-selection-3` — `33:718`
- `business-partner-login-email` — `33:766`
- `business-partner-dashboard//catalog` — `48:2`
- `business-partner-dashboard//orders` — `60:313`
- `business-partner-dashboard//documents` — `72:772`
- `business-partner-dashboard//rules` — `72:1121`
- `business-partner-dashboard//support` — `74:1609`
- `business-partner-dashboard//support` — `74:2049`
- `business-partner-dashboard//support` — `74:2176`
- `business-partner-dashboard//profile` — `80:2342`
- `business-partner-dashboard//drop` — `83:2565`
- `business-partner-dashboard//study` — `155:1749`
- `business-partner-dashboard//support` — `168:624`
- `business-partner-dashboard//support` — `168:782`
- `business-partner-dashboard//support` — `168:944`
- `business-partner-dashboard//rules-2` — `264:2312`
- `business-partner-dashboard//rules-3` — `264:2420`
- `Main-catalog-menu` — `336:1213`
- `Frame 8` — `336:1832`
- `Main-brand-menu` — `336:1960`
- `Main-about-menu` — `342:3438`
- `Product-card//To-order` — `450:1379`
- `Product-card//Business-client` — `450:1771`
- `Product-card//Modified-article` — `450:2175`
- `Catalog//Filters` — `450:2581`
- `Main//Category-open` — `515:6712`
- `Main//Category-open-2` — `515:9081`
- `Brands//Category` — `688:1926`
- `Compare//Glue` — `839:3986`
- `Compare//Empty` — `839:4179`
- `Cart//Empty` — `889:3772`
- `Cart//Empty-closed` — `889:5343`
- `Cart//Adding` — `889:6160`
- `Cart//Checkout-2` — `906:7494`
- `Cart//Checkout-3` — `906:7662`
- `Blog//News` — `914:5798`
- `Blog//Events` — `914:6201`
- `Blog//About-us` — `914:6600`
- `Partners//Pop-up` — `954:6904`
- `Main//Partnetrs-pop-up` — `967:7347`
- `Services//Pop-up` — `986:7985`
- `Favorites` — `1049:7774`
- `Favorite//Pop-up` — `1049:10275`
- `Main-services-menu` — `1050:11407`
- `Sidebar` — `1169:8278`
- `Sidebar hover arrow` — `1169:8370`
- `Main//Sidebar-hover` — `1174:8284`
- `Brand//Products-Subcategory` — `1174:9197`

## Що зверстано за макетом

| Сторінка | Маршрут | Екран Figma | Vue |
|---|---|---|---|
| Хедер / футер | усі | `main-menu-v1`, `Footer` (у `Main`) | `Components/Layout/TheHeader.vue`, `TheFooter.vue` |
| Головна | `/` | `Main` `290:716` | `Pages/Home.vue` |
| Каталог (лендинг) | `/catalog` | `Catalog` `410:772` | `Pages/Catalog.vue` |
| Усі товари | `/catalog/all` | `Catalog//All-products` `782:2685` | `Pages/CatalogCategory.vue` |
| Категорія / підкатегорія | `/catalog/{slug}` | `Catalog-category` `533:15083`, `Catalog//Subcategory` `533:15831` | `Pages/CatalogCategory.vue` |
| Товар | `/p/{slug}` | `Product-card` `441:938` | `Pages/Product.vue` |

Спільні компоненти: `ProductCard`, `CategoryCard`, `ProductCarousel`, `CatalogFilters`, `Stars`, `Qty`, `Icon`. Стилі кнопок, секцій, хлібних крихт лежать у `scss/components/_button.scss` і `_section.scss`.

Фільтри списку товарів (query-параметри): `q`, `brand[]`, `category[]`, `availability[]` (`in_stock` / `preorder`), `sale`, `min_price`, `max_price`, `sort` (`price_asc` / `price_desc`).

### Ще не зроблено

- Мегаменю хедера (`Main-catalog-menu`, `Main-brand-menu` та інші) і випадні списки мови й валюти.
- Блоки головної «Блог та новини» і «SEO-текст»: немає моделі новин і SEO-полів. Тексти секцій «Послуги», «Партнерство» і «Про компанію» тимчасово лежать у `resources/js/data/home.js` і чекають на копірайт і CMS.
- На сторінці товару немає блоків «Рекомендуємо до…», варіантів (колір, розмір), «Інструкцій», «Відеоогляду», карток відгуків і «Нещодавно переглянутих», бо для них немає даних у моделі.
- Фото товарів, категорій і логотипи брендів поки що плейсхолдери.
- Мобільних макетів немає. Адаптив зроблений базовий, без окремого дизайну.
