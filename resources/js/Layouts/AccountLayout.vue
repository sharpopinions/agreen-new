<template>
    <AppLayout>
        <!-- Claude Design → client-cabinet.jsx: ClientCabinet -->
        <div class="cabinet">
            <aside class="cabinet__side">
                <div class="cabinet__hello">
                    <div class="cabinet__avatar">{{ initials }}</div>
                    <div>
                        <div class="cabinet__name">Вітаємо, {{ user.name }}! 👋</div>
                        <div class="cabinet__role">{{ roleLabel }}</div>
                    </div>
                </div>
                <nav class="cabinet__nav" aria-label="Розділи кабінету">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="cabinet__link"
                        :class="{ 'cabinet__link--active': isActive(item.href) }"
                    >
                        <span class="cabinet__icon" aria-hidden="true">{{ item.icon }}</span>{{ item.label }}
                    </Link>
                </nav>
                <button type="button" class="cabinet__link cabinet__link--danger" @click="router.post('/auth/logout')">
                    <span class="cabinet__icon" aria-hidden="true">⏻</span>Вихід
                </button>
            </aside>

            <section class="cabinet__content">
                <p v-if="user.requestedRole" class="cabinet__pending">
                    Заявку на статус «{{ roles[user.requestedRole] }}» отримано — менеджер звʼяжеться з вами після перевірки.
                    До того кабінет працює як для звичайного клієнта.
                </p>
                <div class="cabinet__head">
                    <div>
                        <h1 class="cabinet__title">{{ title }}</h1>
                        <p v-if="subtitle" class="cabinet__subtitle">{{ subtitle }}</p>
                    </div>
                    <slot name="actions" />
                </div>
                <slot />
            </section>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    title:    { type: String, required: true },
    subtitle: { type: String, default: '' },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const roles = {
    client: 'Клієнт',
    business_client: 'Бізнес-клієнт',
    business_partner: 'Бізнес-партнер',
};

const roleLabel = computed(() => roles[user.value.role] ? `Особистий кабінет · ${roles[user.value.role]}` : 'Особистий кабінет');
const initials = computed(() => ((user.value.name?.[0] ?? '') + (user.value.lastName?.[0] ?? '')).toUpperCase());

const nav = [
    { href: '/account',           icon: '⊞', label: 'Каталог' },
    { href: '/account/orders',    icon: '◫', label: 'Замовлення' },
    { href: '/account/documents', icon: '⊟', label: 'Документообіг' },
    { href: '/account/terms',     icon: '◈', label: 'Умови співпраці' },
    { href: '/account/study',     icon: '🎓', label: 'Навчання' },
    { href: '/account/support',   icon: '◉', label: 'Техпідтримка' },
    { href: '/account/personal',  icon: '◎', label: 'Персональні дані' },
    { href: '/account/settings',  icon: '⚙', label: 'Налаштування' },
];

function isActive(href) {
    const path = page.url.split('?')[0];
    return href === '/account' ? path === '/account' : path.startsWith(href);
}
</script>
