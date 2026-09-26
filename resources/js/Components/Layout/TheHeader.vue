<template>
    <header class="header">
        <div class="header__inner container">
            <Link href="/" class="header__logo" aria-label="A-green — на головну">A-green</Link>

            <nav class="header__nav">
                <Link
                    v-for="item in navLinks"
                    :key="item.key"
                    :href="item.href"
                    class="header__nav-link"
                    :class="{ 'header__nav-link--active': isActive(item.href) }"
                >
                    {{ item.label }}
                    <Icon v-if="item.dropdown" name="chevron-down" :size="12" />
                </Link>
            </nav>

            <div class="header__switchers">
                <button class="header__switcher" type="button">UKR <Icon name="chevron-down" :size="12" /></button>
                <button class="header__switcher" type="button">₴ <Icon name="chevron-down" :size="12" /></button>
            </div>

            <div class="header__actions">
                <button v-for="action in actions" :key="action.key" class="header__action" type="button">
                    <Icon :name="action.icon" :size="22" />
                    <span class="header__action-label">{{ action.label }}</span>
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

const page = usePage();

const isActive = (href) => page.url === href || page.url.startsWith(href + '/') || page.url.startsWith(href + '?');

const navLinks = [
    { label: 'Каталог',      key: 'catalog',  href: '/catalog',  dropdown: false },
    { label: 'Бренди',       key: 'brands',   href: '/brands',   dropdown: true  },
    { label: 'Послуги',      key: 'services', href: '/services', dropdown: true  },
    { label: 'Партнерам',    key: 'partners', href: '/partners', dropdown: false },
    { label: 'Про компанію', key: 'about',    href: '/about',    dropdown: true  },
    { label: 'Блог',         key: 'blog',     href: '/blog',     dropdown: false },
    { label: 'Контакти',     key: 'contacts', href: '/contacts', dropdown: false },
];

const actions = [
    { key: 'search',  icon: 'search',  label: 'Пошук'      },
    { key: 'login',   icon: 'user',    label: 'Увійти'     },
    { key: 'compare', icon: 'compare', label: 'Порівняння' },
    { key: 'wish',    icon: 'heart',   label: 'Обране'     },
    { key: 'cart',    icon: 'cart',    label: 'Кошик'      },
];
</script>
