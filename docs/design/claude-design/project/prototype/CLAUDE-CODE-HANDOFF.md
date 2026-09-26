# A-green → Claude Code: Інструкція передачі в розробку

> Готовий пакет для верстки реальної версії. Передай цей файл (і папку `prototype/`) у Claude Code.

---

## Крок 1. Підготуй репозиторій

```bash
# Створи новий проект
npx create-next-app@latest a-green --typescript --tailwind --app
cd a-green

# Скопіюй прототип як референс
mkdir -p design-reference
cp -r /шлях/до/prototype/* design-reference/
```

Структура:
```
a-green/
├── design-reference/          ← наш прототип (референс, не чіпати)
│   ├── A-green Prototype.html
│   ├── design-tokens.json     ← ⭐ головне джерело правди
│   ├── HANDOFF.md             ← ТЗ, API, моделі БД
│   ├── AI-README.md           ← як портувати
│   ├── components.jsx         ← дизайн-система
│   ├── pages.jsx
│   ├── client-cabinet.jsx     ← кабінет клієнта (свіже за ТЗ)
│   ├── auth-modal.jsx         ← вхід/реєстрація (свіже за ТЗ)
│   └── *.jsx, animations.css
└── ... (Next.js проект)
```

---

## Крок 2. Перший промпт у Claude Code

Скопіюй цей текст у Claude Code:

```
Я будую e-commerce A-green на Next.js 14 + Tailwind + shadcn/ui.

У папці design-reference/ лежить готовий HTML/React-прототип — це дизайн,
який треба перенести в продакшн-код.

КРОК 1: Прочитай у такому порядку:
  1. design-reference/AI-README.md       — як портувати
  2. design-reference/design-tokens.json — ВСІ кольори, відступи, радіуси, тіні, анімації
  3. design-reference/HANDOFF.md          — ТЗ, список сторінок, API endpoints, моделі БД
  4. design-reference/components.jsx      — базові компоненти (Btn, Field, Card...)

КРОК 2: Налаштуй дизайн-систему:
  - Перенеси токени з design-tokens.json у tailwind.config.ts та globals.css
    (CSS-змінні для light/dark теми)
  - Підключи шрифт Geist
  - Створи shadcn/ui компоненти на цих токенах

КРОК 3: Не верстай усе одразу. Почни з:
  - Layout (Header + Footer)
  - Головна сторінка (MainPage з pages.jsx)
  - Каталог + картка товару

Питай мене перед кожною новою фазою.
```

---

## Крок 3. Подальші промпти (по фазах)

**Фаза кабінету клієнта** (свіже за ТЗ):
```
Перенеси кабінет клієнта з design-reference/client-cabinet.jsx.
Sidebar: Каталог, Замовлення, Документообіг, Умови співпраці,
Навчання, Техпідтримка, Персональні дані, Налаштування, Вихід.
Статуси замовлень (7 шт) — з ORDER_STATUSES в тому ж файлі.
```

**Фаза авторизації:**
```
Перенеси модальний попап з design-reference/auth-modal.jsx:
Вхід / Реєстрація / Підтвердження email / Вибір ролі (4 картки).
Інтегруй з NextAuth.js.
```

---

## Що Claude Code візьме автоматично

| З файлу | Що отримає |
|---------|-----------|
| `design-tokens.json` | Кольори (light+dark), spacing scale, radius, shadow, typography, **animation keyframes + easing + timing** |
| `components.jsx` | Готова логіка Btn/Field/Badge/ProductCard/CatCard/QtyCtrl |
| `*.jsx` сторінки | JSX-структура кожного екрану + state-логіка |
| `animations.css` | CSS-анімації 1:1 |
| `HANDOFF.md` | API endpoints, моделі БД (User, Product, Order...), чек-лист по 7 фазах |

---

## Важливі правила для Claude Code (вже в AI-README.md)

✅ **Копіювати:** логіку компонентів, JSX-структуру, значення токенів, розміри, анімації
❌ **НЕ копіювати буквально:** inline-стилі (→ Tailwind), localStorage для роутингу (→ Next router), hash-градієнт плейсхолдери (→ реальні фото з CDN)

---

## Альтернатива: через GitHub

```bash
git init && git add . && git commit -m "design reference"
git remote add origin <твій-repo>
git push
```
Потім дай Claude Code посилання на репо — він працюватиме з файлами напряму.

---

## TL;DR

1. Скачай папку `prototype/`
2. Поклади в `design-reference/` нового Next.js проекту
3. Дай Claude Code промпт з Кроку 2 (головне — вказати на `design-tokens.json` + `AI-README.md`)
4. Веди по фазах із HANDOFF.md

Усі кольори, відступи, анімації — у `design-tokens.json`, машиночитно. Claude Code витягне їх точно.
