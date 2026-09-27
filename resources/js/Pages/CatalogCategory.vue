<template>
    <AppLayout>
        <div class="cat-page">

            <!-- Хлібні крихти -->
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/catalog" class="breadcrumbs__item">Каталог</Link>
                <template v-if="category?.parent">
                    <span class="breadcrumbs__sep">›</span>
                    <Link :href="`/catalog/${category.parent.slug}`" class="breadcrumbs__item">{{ category.parent.name }}</Link>
                </template>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ title }}</span>
            </nav>

            <!-- Заголовок -->
            <h1 class="cat-page__title">{{ title }}</h1>
            <p class="cat-page__subtitle">{{ category?.shortDescription || 'Оберіть категорію або скористайтесь пошуком. Для партнерів — гнучка система знижок!' }}</p>

            <!-- Пошук -->
            <div class="cat-page__search-wrap">
                <svg class="cat-page__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input v-model="query" class="cat-page__search" type="search" placeholder="Введіть назву товару або категорії..." @keydown.enter="search" />
            </div>

            <!-- Основний лейаут -->
            <div class="cat-layout">
                <CatalogFilters
                    :key="baseUrl"
                    :base-url="baseUrl"
                    :filters="filters"
                    :categories="categories"
                    :category-title="category ? 'Підкатегорія' : 'Категорія'"
                    :brands="brands"
                    :price-range="priceRange"
                    :attribute-filters="attributeFilters"
                />
                <CatalogResults :base-url="baseUrl" :products="products" :filters="filters" />
            </div>

            <!-- SEO-текст категорії з адмінки (HTML очищено на сервері) -->
            <section v-if="category?.description" class="cat-page__seo rich-text" v-html="category.description"></section>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CatalogFilters from '@/Components/CatalogFilters.vue';
import CatalogResults from '@/Components/CatalogResults.vue';

const props = defineProps({
    category:   Object,
    categories: Array,
    brands:     Array,
    products:   Object,
    filters:    Object,
    priceRange: Object,
    attributeFilters: { type: Array, default: () => [] },
});

const title   = computed(() => props.category?.name ?? 'Усі товари');
const baseUrl = computed(() => props.category ? `/catalog/${props.category.slug}` : '/catalog/all');
const query   = ref(props.filters.q ?? '');

function search() {
    router.get(baseUrl.value, query.value.trim() ? { q: query.value.trim() } : {}, { preserveScroll: true });
}
</script>
