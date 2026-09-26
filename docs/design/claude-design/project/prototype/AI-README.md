# A-green — Опис дизайну для AI-розробника

> Цей файл — структурований опис для Claude Code / GPT / іншого AI, який буде верстати реальну версію.

## Як використовувати цей файл

1. **Спочатку прочитай HANDOFF.md** — там вся технічна інформація
2. **Прочитай файли в такому порядку:**
   - `components.jsx` — дизайн-система (токени, базові компоненти)
   - `App.jsx` — глобальний стан і роутинг
   - `pages.jsx` — основні сторінки
   - `extra-pages*.jsx` — додаткові сторінки за потребою

## Команди для Claude Code

```bash
# Спочатку:
cat prototype/HANDOFF.md

# Дізайн-система:
cat prototype/components.jsx | head -200    # токени + theme
cat prototype/components.jsx | head -400    # + атоми

# Конкретна сторінка (приклад):
grep -n "function ProductPage" prototype/pages.jsx
grep -n "function CartPage" prototype/pages.jsx

# Знайти стиль кнопки:
grep -A 20 "function Btn" prototype/components.jsx
```

## Перенесення в Next.js + Tailwind

### Крок 1: токени → Tailwind config

```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        bg:      'hsl(0 0% 100%)',
        'bg-alt':'hsl(240 5% 96%)',
        border:  'hsl(240 6% 90%)',
        // ... з components.jsx makeTheme()
      },
      borderRadius: {
        sm: '8px',
        DEFAULT: '10px',
        lg: '14px',
      },
      fontFamily: {
        sans: ['Geist', 'Inter', 'sans-serif'],
      },
    },
  },
};
```

### Крок 2: компонент → React

Кожен компонент з `components.jsx` (`Btn`, `Field`, `ProductCard`) — це готова шаблонка. Просто:
1. Замінити inline styles на Tailwind classes
2. Замінити `T.text` на `text-foreground` (або pure HEX)
3. Замінити `T.border` на `border-border`

### Крок 3: сторінки → app/router

Файл `pages.jsx` має `MainPage`, `CatalogPage`, `ProductPage`, `CartPage`, `CheckoutPage`, `DashboardPage`. Кожна сторінка → окремий `page.tsx`:

```
app/
├── page.tsx                  ← MainPage
├── catalog/
│   ├── page.tsx              ← CatalogPage
│   └── [category]/
│       └── [subcategory]/
│           └── page.tsx       ← CatalogSubcategoryPage
├── product/[slug]/page.tsx   ← ProductPage
├── cart/page.tsx             ← CartPage
├── checkout/page.tsx         ← CheckoutPage
└── dashboard/
    ├── page.tsx              ← ExtendedDashboard (Каталог)
    ├── orders/page.tsx
    ├── documents/page.tsx
    ├── support/page.tsx
    └── ...
```

### Крок 4: state → Zustand

```ts
// stores/cart.ts
import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useCart = create(persist((set) => ({
  items: [],
  addItem: (product) => set((s) => ({ /* ... */ })),
  removeItem: (id) => set((s) => ({ /* ... */ })),
}), { name: 'cart' }));
```

## Що НЕ копіювати буквально

- ❌ Inline стилі — переклади на Tailwind / styled
- ❌ `window.claude.complete` — це для прототипу, в проді не треба
- ❌ `localStorage` для page state — там використовуй Next.js router
- ❌ Hash-based gradient placeholder в `<Img>` — заміни на справжні зображення

## Що СКОПІЮВАТИ як є

- ✅ Логіку компонентів (state, handlers, conditional rendering)
- ✅ Розмітку (JSX структуру)
- ✅ Колірні значення (для дизайн-системи)
- ✅ Розміри (padding, gap, font-size)
- ✅ Анімації з `animations.css`

## Запитання до автора дизайну

Якщо щось незрозуміло — спочатку перевір сам файл `index.html` у браузері. Cmd+K показує всі сторінки. Все що клікабельне — повинно так само працювати у проді.
