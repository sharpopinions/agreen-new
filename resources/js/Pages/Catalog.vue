<template>
    <AppLayout>
        <div class="catalog-landing">

            <!-- Пошук -->
            <div class="catalog-landing__search-wrap">
                <svg class="catalog-landing__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input v-model="query" class="catalog-landing__search" type="search" placeholder="Введіть назву товару або категорії..." @keydown.enter="search" />
            </div>

            <!-- Категорії -->
            <div class="catalog-landing__section-label">ВСІ КАТЕГОРІЇ</div>

            <div class="catalog-landing__grid">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/catalog/${cat.slug}`"
                    class="catalog-landing__card"
                >
                    <div class="catalog-landing__card-name">{{ cat.name }}</div>
                    <div class="catalog-landing__card-count">{{ pluralUa(cat.count ?? 0, 'товар', 'товари', 'товарів') }}</div>
                    <ul v-if="cat.children?.length" class="catalog-landing__card-subs">
                        <li v-for="sub in cat.children.slice(0, 3)" :key="sub.id">— {{ sub.name }}</li>
                    </ul>
                </Link>
            </div>

            <!-- Фільтри + товари -->
            <div class="cat-layout">
                <CatalogFilters
                    base-url="/catalog"
                    :filters="filters"
                    :categories="categories"
                    :brands="brands"
                    :price-range="priceRange"
                />
                <CatalogResults base-url="/catalog" :products="products" :filters="filters" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CatalogFilters from '@/Components/CatalogFilters.vue';
import CatalogResults from '@/Components/CatalogResults.vue';
import { pluralUa } from '@/utils/format';

const props = defineProps({
    categories: Array,
    brands:     Array,
    products:   Object,
    filters:    Object,
    priceRange: Object,
});

const query = ref(props.filters.q ?? '');

// Пошук за назвою або артикулом
function search() {
    router.get('/catalog', query.value.trim() ? { q: query.value.trim() } : {}, { preserveScroll: true });
}
</script>
