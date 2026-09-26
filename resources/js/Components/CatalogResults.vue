<template>
    <main class="cat-main">
        <div class="cat-toolbar">
            <span class="cat-toolbar__count">Знайдено: <strong>{{ pluralUa(products.total, 'товар', 'товари', 'товарів') }}</strong></span>
            <div class="cat-toolbar__right">
                <select class="cat-toolbar__sort" :value="filters.sort" @change="changeSort">
                    <option value="popular">Популярні</option>
                    <option value="price_asc">Ціна: від низької</option>
                    <option value="price_desc">Ціна: від високої</option>
                </select>
                <div class="cat-toolbar__view">
                    <button class="cat-toolbar__view-btn" :class="{ 'cat-toolbar__view-btn--active': viewMode === 'grid' }" @click="viewMode = 'grid'" aria-label="Плитка">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
                    </button>
                    <button class="cat-toolbar__view-btn" :class="{ 'cat-toolbar__view-btn--active': viewMode === 'list' }" @click="viewMode = 'list'" aria-label="Список">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="products.data.length" :class="viewMode === 'grid' ? 'cat-grid' : 'cat-list'">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" :mode="viewMode" />
        </div>

        <div v-else class="cat-empty">
            <div class="cat-empty__icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
            <p class="cat-empty__title">Нічого не знайдено</p>
            <p class="cat-empty__text">Спробуйте змінити фільтри</p>
            <button class="cat-empty__btn" @click="router.get(baseUrl)">Скинути фільтри</button>
        </div>

        <div v-if="products.last_page > 1" class="cat-pagination">
            <Link
                v-for="link in pageLinks"
                :key="link.label"
                :href="link.url"
                class="cat-pagination__btn"
                :class="{ 'cat-pagination__btn--active': link.active }"
                preserve-scroll
            >{{ link.label }}</Link>
        </div>
    </main>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import ProductCard from '@/Components/ProductCard.vue';
import { pluralUa } from '@/utils/format';

const props = defineProps({
    baseUrl:  { type: String, required: true },
    products: { type: Object, required: true },
    filters:  { type: Object, required: true },
});

const viewMode = ref('grid');

// Лише номери сторінок (без «Previous» / «Next»)
const pageLinks = computed(() => props.products.links.filter(l => l.url && /^\d+$/.test(l.label)));

function changeSort(e) {
    const url = new URL(window.location.href);
    url.searchParams.delete('page');
    if (e.target.value === 'popular') url.searchParams.delete('sort');
    else url.searchParams.set('sort', e.target.value);
    router.get(url.pathname + url.search, {}, { preserveScroll: true, preserveState: true });
}
</script>
