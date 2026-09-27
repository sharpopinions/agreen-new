<template>
    <header class="header" @keydown.esc="closeMega" @focusout="onFocusOut">
        <div class="header__topbar">
            <span>Пн–Пт 9:00–18:00 · +380 44 123-45-67</span>
            <div class="header__topbar-actions">
                <a href="#">Особистий кабінет</a>
                <span>·</span>
                <span>UKR</span>
                <button class="header__theme-btn" @click="toggleTheme">
                    <svg v-if="isDark" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                    <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    {{ isDark ? 'Світла' : 'Темна' }}
                </button>
            </div>
        </div>

        <div class="header__main">
            <Link href="/" class="header__logo">
                <div class="header__logo-mark">AG</div>
                <span class="header__logo-name">A-green</span>
            </Link>

            <nav class="header__nav" @mouseleave="scheduleClose">
                <Link
                    v-for="item in navLinks"
                    :key="item.key"
                    :href="item.href"
                    class="header__nav-btn"
                    :class="{ 'header__nav-btn--active': page.url.startsWith(item.href) || mega === item.mega && item.mega }"
                    :aria-expanded="item.mega ? String(mega === item.mega) : undefined"
                    @mouseenter="openMega(item.mega)"
                    @focus="openMega(item.mega)"
                >
                    {{ item.label }}
                    <svg v-if="item.mega" class="header__nav-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </Link>
            </nav>

            <div class="header__search-wrap">
                <svg class="header__search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input class="header__search" type="text" placeholder="Пошук..." />
                <span class="header__search-hint">⌘К</span>
            </div>

            <div class="header__actions">
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

        <!-- Мегаменю (Claude Design: mega-menus.jsx) -->
        <div v-if="mega" class="header__mega" @mouseenter="openMega(mega)" @mouseleave="scheduleClose">
            <MegaMenu :kind="mega" :nav="page.props.nav" />
        </div>
    </header>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import MegaMenu from '@/Components/Layout/MegaMenu.vue';

const page = usePage();

// Мегаменю: відкривається при наведенні/фокусі, закривається з невеликою
// затримкою, щоб встигнути перевести курсор з пункту меню на панель
const mega = ref(null);
let closeTimer = null;

function openMega(kind) {
    clearTimeout(closeTimer);
    mega.value = kind || null;
}

function scheduleClose() {
    clearTimeout(closeTimer);
    closeTimer = setTimeout(() => { mega.value = null; }, 150);
}

function closeMega() {
    clearTimeout(closeTimer);
    mega.value = null;
}

function onFocusOut(e) {
    if (!e.currentTarget.contains(e.relatedTarget)) closeMega();
}

const removeNavigateListener = router.on('navigate', closeMega);
onBeforeUnmount(() => { removeNavigateListener(); clearTimeout(closeTimer); });

const isDark = ref(document.documentElement.getAttribute('data-theme') === 'dark');

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : 'light');
}

const navLinks = [
    { label: 'Каталог',   key: 'catalog',  href: '/catalog',  mega: 'catalog'  },
    { label: 'Бренди',    key: 'brands',   href: '/brands',   mega: 'brands'   },
    { label: 'Послуги',   key: 'services', href: '/services', mega: 'services' },
    { label: 'Партнерам', key: 'partners', href: '/partners', mega: null       },
    { label: 'Про нас',   key: 'about',    href: '/about',    mega: 'about'    },
    { label: 'Блог',      key: 'blog',     href: '/blog',     mega: null       },
];
</script>
