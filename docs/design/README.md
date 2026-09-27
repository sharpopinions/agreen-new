# Дизайн A-green

## Джерела

- **Візуальний дизайн** (кольори, шрифти, картки, кнопки) — проєкт у Claude Design («A-green Prototype»). У коді він зафіксований у токенах `resources/scss/abstracts/_variables.scss` і в компонентах. Нові сторінки робимо в цьому стилі.
- **Прототип Figma** — [A-green](https://www.figma.com/design/UsUspef6txX7t8aLyK5Pv8/A-green), чорно-білий вайрфрейм. Він задає **структуру** сторінок: які блоки є і в якому порядку. Кольори й форми з нього **не** переносимо.
- **Таблиця ТЗ** — [ТЗ по сайту Агрін](https://docs.google.com/spreadsheets/d/17ZqtYuUHBDx-NYJZpcxO-u-ZDARXwo_qfwZFrTFWKYA/edit): 35 сторінок, ТЗ і прототип для кожної.

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

## Каталог: маршрути та фільтри

| Маршрут | Сторінка | Vue |
|---|---|---|
| `/catalog` | плитки категорій + усі товари з фільтрами | `Pages/Catalog.vue` |
| `/catalog/all` | усі товари з фільтрами | `Pages/CatalogCategory.vue` |
| `/catalog/{slug}` | категорія або підкатегорія (разом з товарами підкатегорій) | `Pages/CatalogCategory.vue` |

Фільтри (`Components/CatalogFilters.vue`) і список товарів (`Components/CatalogResults.vue`) спільні для всіх трьох сторінок.

Query-параметри: `q` (назва або артикул), `brand[]`, `category[]`, `availability[]` (`in_stock` / `preorder`), `sale`, `min_price`, `max_price`, `sort` (`price_asc` / `price_desc`), `page`.

## Прогрес верстки за Claude Design

Референс: `docs/design/claude-design/project/prototype/` (`pages.jsx`, `components.jsx`, `extra-pages*.jsx`).

- [x] Головна (`MainPage`): усі 8 блоків. Контент блоків 5–8 тимчасово лежить у `resources/js/data/home.js`.
- [x] Мегаменю хедера (`mega-menus.jsx`): категорії й бренди з бази через спільні дані Inertia (`HandleInertiaRequests::shareOnce`)
- [x] Пошук Cmd+K / Ctrl+K (`command-palette.jsx`): сторінки, категорії, бренди, дія «Перемкнути тему», товари через `GET /search?q=`
- [x] Сторінка товару (`ProductPage`), стани за ТЗ:
  - в наявності (кількість обмежена залишком);
  - під замовлення: ціна прихована, строк з `preorder_days`;
  - знято з виробництва: `replaced_by_id` → «Доступна заміна»;
  - рекомендуємо новішу модель: заміна є, товар ще в наявності;
  - попередження `warning_text` з адмінки;
  - блок «Нещодавно переглянуті».
  - [ ] Бізнес-клієнт: `partnerPrice` уже передається, потрібні авторизація і ціни з 1С
  - [ ] Крихти «через бренд»: після сторінок брендів
  - [ ] Варіації (колір, розмір), інструкції, картки відгуків: немає даних у моделі
- [x] Кошик, оформлення, подяка:
  - кошик у сесії (`App\Services\Cart`), кількість обмежена залишком;
  - попап «Додано до кошика» по центру (ТЗ);
  - бічна панель кошика;
  - сторінка кошика з порожнім станом і 3 товарами;
  - оформлення в 3 кроки, доставка й оплата за ТЗ;
  - звичайні товари й передзамовлення → два окремі замовлення, передзамовлення без оплати й доставки;
  - замовлення в таблицях `orders` / `order_items`, розділ «Продажі → Замовлення» в адмінці;
  - тести: `tests/Feature/CheckoutTest.php`.
  - [ ] Вхід для зареєстрованих, «Відстрочка платежу» для бізнес-партнерів: після авторизації
  - [ ] Листи / SMS про статус замовлення, онлайн-оплата: потрібні сервіси й ключі
  - [ ] Попап «Швидке замовлення»
- [ ] Інші сторінки (`extra-pages*.jsx`)
- [ ] Вхід і реєстрація, кабінети

Заглушка фото `Components/ImgPlaceholder.vue` — порт `Img` з прототипу. Її треба замінити на реальні зображення.
