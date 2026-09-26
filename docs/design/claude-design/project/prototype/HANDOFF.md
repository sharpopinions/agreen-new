# A-green E-commerce — Developer Handoff

> Дизайн-система та інтерактивний прототип B2B/B2C e-commerce платформи для промислових і гігієнічних товарів.

---

## 📁 Структура проекту

```
prototype/
├── A-green Prototype.html       ← Точка входу (відкрити в браузері)
├── animations.css                ← Глобальні анімації + reduced-motion
├── components.jsx                ← Дизайн-система: theme, токени, базові компоненти
├── pages.jsx                     ← Основні сторінки (Main, Catalog, Product, Cart, Checkout, Dashboard)
├── App.jsx                       ← Кореневий компонент + роутинг + state
├── command-palette.jsx           ← Cmd+K командна палітра
├── mega-menus.jsx                ← Mega-меню для шапки
├── extra-pages.jsx               ← 404, About, Brands, Blog, Services, Contacts, Favorites, Sale, Partners, Delivery
├── extra-pages2.jsx              ← Compare, Vacancies, Login, Thank-Order/Form, Privacy/Public-offer/Return
├── extra-pages3.jsx              ← Brand-Products, Extended Dashboard
├── extra-pages4.jsx              ← Cart-Empty, Partners-Popup, Aktsyii, Role-selection
├── extra-pages5.jsx              ← Email-confirm, Login-email, Client-dashboard, Cart-Drawer
├── extra-pages6.jsx              ← Aktsyia (детальна сторінка акції)
├── extra-pages7.jsx              ← Catalog-Subcategory, Study-center
├── extra-pages8.jsx              ← Brand-Detail (About/Media), Catalog-category
├── extra-pages9.jsx              ← Blog-Event, Blog filtered states
├── extra-pages10.jsx             ← Favorite Pop-up
├── extra-pages11.jsx             ← Services-popup, Brands-category, Brand-subcategory
└── extra-pages12.jsx             ← Dashboard variations (Rules, Support, Role-confirmed)
```

---

## 🎨 Дизайн-система

### Колірні токени (shadcn/ui style)

#### Light Theme
```js
{
  bg:      '#ffffff',   // Основний фон
  bgAlt:   '#f4f4f5',   // Альтернативний фон (zinc-100)
  surface: '#ffffff',   // Поверхня карток
  border:  '#e4e4e7',   // Основні бордери (zinc-200)
  border2: '#d4d4d8',   // Активні бордери (zinc-300)
  text:    '#09090b',   // Основний текст (zinc-950)
  text2:   '#71717a',   // Вторинний (zinc-500)
  text3:   '#a1a1aa',   // Третинний (zinc-400)
  inv:     '#09090b',   // Інверсний (для primary btn)
  invBg:   '#18181b',   // Inverse background
  invText: '#fafafa',   // Inverse text
  muted:   '#f4f4f5',
  warn:    '#b45309',
  danger:  '#dc2626',
}
```

#### Dark Theme
```js
{
  bg:      '#09090b',   // zinc-950
  bgAlt:   '#18181b',   // zinc-900
  surface: '#09090b',
  border:  '#27272a',   // zinc-800
  border2: '#3f3f46',   // zinc-700
  text:    '#fafafa',   // zinc-50
  text2:   '#a1a1aa',
  text3:   '#71717a',
  inv:     '#fafafa',
  invBg:   '#fafafa',
  invText: '#09090b',
  muted:   '#18181b',
  warn:    '#f59e0b',
  danger:  '#ef4444',
}
```

### Border radius
- `rSm`: `8px` — інпути, малі кнопки, тонкі бейджі
- `r`: `10px` — стандартний
- `rLg`: `14px` — картки, модалки, dropdown
- `rPill`: `9999px` — пілюлі, чекбокси, статус-бейджі

### Тіні
- `shadow`: `0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04)` — спокій
- `shadowMd`: `0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04)` — hover

### Типографія
- **Font family**: `Geist, Inter, -apple-system, sans-serif`
- **Heading letter-spacing**: `-0.025em` (`-0.03em` для display)
- **Розміри**:
  - Display: `28-32px` weight 700-800
  - H1: `24px` weight 700
  - H2: `18-22px` weight 600-700
  - Body: `14px` weight 400
  - Caption: `12-13px` weight 400-500
  - Label/uppercase: `11px` weight 600, `letter-spacing: 0.06-0.08em`

### Spacing
- 4px scale: `4, 6, 8, 10, 12, 14, 16, 20, 24, 28, 32, 40, 48, 60`
- Layout padding: `60px` (sides), `28-32px` (sections)
- Grid gap: `12-20px`

---

## 🧩 Компонентна бібліотека

### Btn
```jsx
<Btn variant="primary|secondary|ghost|outline|danger"
     size="sm|md|lg"
     full
     disabled
     onClick={...}>
  Text
</Btn>
```
- `primary`: T.invBg бекграунд, T.invText текст
- `secondary`: T.surface + T.border
- `ghost`: transparent + hover muted
- `outline`: transparent + T.border
- `danger`: red

### Field
```jsx
<Field label="Email" placeholder="..." type="email"
       value={v} onChange={setV} required/>
```
Має focus ring (`outline: 2px solid T.invBg + offset 2px`)

### Badge
```jsx
<Badge label="Хіт|Акція|Новинка"/>
```
Кольорове кодування за лейблом.

### ProductCard
```jsx
<ProductCard product={p} onView={fn} onAdd={fn}/>
```
Hover-lift, wishlist heart, бейджі, ціна (стара/нова), наявність.

### CatCard
```jsx
<CatCard cat={catObj} onSelect={fn}/>
```

### Crumbs (Breadcrumbs)
```jsx
<Crumbs items={['Каталог', 'Категорія']} setPage={setPage}/>
```

### QtyCtrl
```jsx
<QtyCtrl qty={1} setQty={setN}/>
```

### Img (Placeholder)
```jsx
<Img h={200} label="фото товару" seed="optional"/>
```
Генерує deterministic градієнт на основі label/seed.

### Stars
```jsx
<Stars rating={4.5}/>
```

---

## 🗺️ Карта роутів (40+ сторінок)

### Public
- `main` — Головна
- `catalog`, `catalog-category`, `catalog-sub` — Каталог + категорія + підкатегорія
- `product`, `product-business`, `product-order`, `product-modified`, `product-brand` — Картка товару (5 варіацій)
- `brands`, `brand-products`, `brand-about`, `brand-media`, `brands-category`, `brand-subcategory` — Бренди
- `cart`, `cart-empty`, `checkout` — Кошик і оформлення
- `favorites`, `compare` — Списки бажань і порівняння
- `aktsyii` (список), `aktsyia` (детально), `sale` — Акції
- `services`, `services-popup`, `study-center` — Послуги
- `blog`, `blog-post`, `blog-event`, `blog-news`, `blog-events`, `blog-about` — Блог
- `about`, `contacts`, `partners-popup`, `vacancies`, `delivery` — Інформаційні
- `privacy`, `public-offer`, `return` — Юридичні
- `thank-order`, `thank-form`, `404`

### Auth & Dashboard
- `login`, `login-email`, `email-confirm`, `role-selection`, `role-confirmed`
- `client-dashboard` — Кабінет звичайного клієнта
- `ext-dashboard` — Кабінет бізнес-партнера (8 секцій)
- `rules-program`, `support-list`, `support-thread` — Окремі стани дашборду

---

## 🔌 Глобальний стан (App.jsx)

```js
const [dark, setDark]         = useState  // тема → localStorage 'ag_dark'
const [page, setPage]         = useState  // роутинг → localStorage 'ag_page'
const [cart, setCart]         = useState  // [{ product, qty }] → 'ag_cart'
const [wishlist, setWishlist] = useState  // [Product] → 'ag_wishlist'
const [cartDrawer, ...]       = useState  // bool — drawer відкритий
const [cartPopup, ...]        = useState  // { product } — toast після додавання
const [cmdOpen, ...]          = useState  // bool — Cmd+K
```

---

## 🛠 Технічний стек для впровадження

### Рекомендую
- **Framework**: Next.js 14 (App Router) або Remix
- **Styling**: Tailwind CSS + shadcn/ui (вже на одних токенах)
- **State**: Zustand або React Query
- **Forms**: React Hook Form + Zod
- **Auth**: NextAuth.js / Clerk / Lucia
- **DB**: PostgreSQL + Prisma або Drizzle
- **CMS**: для блогу/новин — Sanity, Strapi, або власна адмінка
- **Search**: Algolia / MeiliSearch для Cmd+K
- **CDN**: Cloudinary / Vercel Image Optimization для фото товарів

### Backend API (мінімум)
```
GET    /api/products?cat=&brand=&priceFrom=&priceTo=&page=
GET    /api/products/:id
GET    /api/categories
GET    /api/brands
GET    /api/brands/:id
POST   /api/cart
PATCH  /api/cart/items/:id
POST   /api/orders
GET    /api/orders (auth)
POST   /api/auth/login
POST   /api/auth/register
POST   /api/auth/verify-email
GET    /api/blog/posts
GET    /api/blog/posts/:slug
POST   /api/contact-form
POST   /api/partner-application
```

### Моделі БД
```
User (id, email, phone, role: client|business|dealer, companyName, edrpou, level, bonusPoints)
Product (id, sku, name, slug, description, price, oldPrice, stock, catId, brandId, badges[], specs{})
Category (id, name, slug, parentId, count)
Brand (id, name, slug, country, description, logoUrl)
Order (id, userId, items[], total, status, deliveryType, paymentType, address)
CartItem (id, userId|sessionId, productId, qty)
WishlistItem (id, userId, productId)
SupportTicket (id, userId, subject, status, messages[])
Discount (id, userId, percent, type, validFrom, validTo)
BlogPost (id, title, slug, category, body, publishedAt, readTime)
```

---

## 📋 Чек-лист впровадження

### Phase 1: Базовий UI (1-2 тижні)
- [ ] Налаштувати Next.js + Tailwind з токенами з цього handoff
- [ ] Портувати компоненти: Btn, Field, Badge, ProductCard, CatCard, QtyCtrl
- [ ] Зверстати Header + Footer + Hero
- [ ] Підключити Geist шрифт

### Phase 2: Каталог (1-2 тижні)
- [ ] Сторінка каталогу з фільтрами
- [ ] Сторінка категорії
- [ ] Сторінка підкатегорії
- [ ] Картка товару (5 варіацій через props)
- [ ] Порівняння товарів

### Phase 3: Кошик (1 тиждень)
- [ ] CartDrawer (slide-in)
- [ ] Сторінка кошика
- [ ] 3-кроковий checkout
- [ ] Cart-Adding toast
- [ ] Thank-you сторінки

### Phase 4: Авторизація (1 тиждень)
- [ ] Login / Login-email (OTP)
- [ ] Registration + Email-confirm
- [ ] Role-selection
- [ ] Захищені роути

### Phase 5: Дашборд (2 тижні)
- [ ] Client-dashboard
- [ ] Business-partner dashboard (8 секцій)
- [ ] Програма лояльності
- [ ] Підтримка (тікети + chat)
- [ ] Дропшипінг

### Phase 6: Контент (1 тиждень)
- [ ] Блог (типи: news, events, about-us)
- [ ] About, Contacts, Vacancies
- [ ] Юридичні сторінки

### Phase 7: Інтеракції (1 тиждень)
- [ ] Cmd+K палітра (Algolia/MeiliSearch)
- [ ] Mega-меню
- [ ] Favorite-popup
- [ ] Анімації

---

## 🎯 Як читати прототип

1. Відкрити `prototype/A-green Prototype.html` в браузері
2. **Cmd+K** — швидка навігація між сторінками
3. **Tweaks** — перемикач теми
4. Для розробника: відкрити `*.jsx` файли — це джерело правди для верстки
5. Усі компоненти використовують одні токени (`T.text`, `T.border` тощо)

---

## 💡 Як Claude (або інший AI) може читати верстку

```bash
# Структура файлів зрозуміла з імен (pages, components, extras)
# Кожна сторінка — самостійна React-функція, копіюється легко
# Дизайн-токени централізовані в makeTheme() в components.jsx
# Усі компоненти експортуються в window для крос-файлової взаємодії
```

**Підказка для AI-розробника**: спочатку прочитати `components.jsx` (токени + базові компоненти), потім `App.jsx` (state + роутинг), потім потрібні сторінки. У файлі є коментарі-якорі `// ── SECTION ──` для навігації.

---

## 📝 Контакт із автором дизайну

Усі питання щодо прототипу, додаткові варіанти екранів, або зміни — звертайтесь до дизайнера, який створив цей макет.

---

**Версія**: 1.0
**Дата**: 25 травня 2026
**Покриття Figma**: ~94% (всі ключові сценарії)
**Кількість екранів**: 40+ унікальних сторінок + 5 варіацій продуктової картки
