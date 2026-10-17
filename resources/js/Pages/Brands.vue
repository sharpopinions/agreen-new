<template>
    <AppLayout>
        <!-- Claude Design → extra-pages.jsx: BrandsPage -->
        <div class="info-page brands-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Бренди</span>
            </nav>

            <h1 class="info-page__title brands-page__title">Бренди</h1>
            <p class="brands-page__subtitle">Ми представляємо провідних виробників у своїх категоріях</p>

            <div class="cat-page__search-wrap brands-page__search">
                <svg class="cat-page__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input v-model="search" class="cat-page__search" type="search" placeholder="Введіть назву бренду..." />
            </div>

            <div v-if="categories.length" class="pill-tabs" role="tablist">
                <button type="button" class="pill-tabs__item" :class="{ 'pill-tabs__item--active': cat === null }" @click="cat = null">Всі</button>
                <button
                    v-for="c in categories"
                    :key="c.id"
                    type="button"
                    class="pill-tabs__item"
                    :class="{ 'pill-tabs__item--active': cat === c.id }"
                    @click="cat = c.id"
                >{{ c.name }}</button>
            </div>

            <div v-if="filtered.length" class="brands-grid">
                <Link v-for="b in filtered" :key="b.id" :href="`/brands/${b.slug}`" class="brand-card">
                    <span class="brand-card__logo">
                        <img v-if="b.logo" :src="b.logo" :alt="b.name" loading="lazy" />
                        <template v-else>{{ b.name.charAt(0) }}</template>
                    </span>
                    <span class="brand-card__name">{{ b.name }}</span>
                    <span class="brand-card__link">Переглянути товари →</span>
                </Link>
            </div>
            <div v-else class="brands-page__empty">Бренд не знайдено</div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    brands:     { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const search = ref('');
const cat    = ref(null);

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    return props.brands.filter(b =>
        (!q || b.name.toLowerCase().includes(q)) &&
        (cat.value === null || b.categories.includes(cat.value)));
});
</script>
