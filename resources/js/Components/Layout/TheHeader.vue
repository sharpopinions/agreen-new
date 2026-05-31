<template>
    <header class="header">
        <div class="header__topbar">
            <span>Пн–Пт 9:00–18:00 · +380 44 123-45-67</span>
            <div class="header__topbar-actions">
                <a href="#">Особистий кабінет</a>
                <span>·</span>
                <span>UKR</span>
            </div>
        </div>

        <div class="header__main">
              <button class="header__logo">
                  <div class="header__logo-mark">AG</div>
                  <span class="header__logo-name">A-green</span>
              </button>

              <nav class="header__nav">
                  <Link
                      v-for="item in navLinks"
                      :key="item.key"
                      :href="item.href"
                      class="header__nav-btn"
                      :class="{ 'header__nav-btn--active': page.url.startsWith(item.href) }"
                  >
                      {{ item.label }}
                  </Link>
              </nav>
  
              <div class="header__actions">
                  <button class="header__icon-btn" aria-label="Пошук">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                      </svg>
                  </button>
  
                  <button class="header__icon-btn" aria-label="Вішліст">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                      </svg>
                  </button>

                  <button class="header__icon-btn" aria-label="Порівняння">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                      </svg>
                  </button>

                  <button class="header__cart-btn">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                          <line x1="3" y1="6" x2="21" y2="6"/>
                          <path d="M16 10a4 4 0 0 1-8 0"/>
                      </svg>
                      Кошик
                  </button>
              </div>
          </div>

    </header>
</template>

<script setup>
    import { Link, usePage } from '@inertiajs/vue3';
    
    const page = usePage();

    const navLinks = [
        { label: 'Каталог',   key: 'catalog',  href: '/catalog' },
        { label: 'Бренди',    key: 'brands',   href: '/brands'  },
        { label: 'Послуги',   key: 'services', href: '/services'},
        { label: 'Партнерам', key: 'partners', href: '/partners'},
        { label: 'Про нас',   key: 'about',    href: '/about'   },
        { label: 'Блог',      key: 'blog',     href: '/blog'    },
    ];
  </script>