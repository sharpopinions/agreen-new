<template>
    <AppLayout>
        <div class="container">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <Link href="/catalog" class="breadcrumbs__item">Каталог</Link>
                <Link v-if="category?.parent" :href="`/catalog/${category.parent.slug}`" class="breadcrumbs__item">
                    {{ category.parent.name }}
                </Link>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ title }}</span>
            </nav>

            <div class="listing">
                <CatalogFilters
                    :key="baseUrl"
                    :base-url="baseUrl"
                    :filters="filters"
                    :categories="categories"
                    :category-title="category ? 'Підкатегорія' : 'Категорія'"
                    :brands="brands"
                    :price-range="priceRange"
                />

                <main class="listing__main">
                    <div v-if="category && categories.length" class="listing__subs">
                        <Link
                            v-for="sub in categories"
                            :key="sub.id"
                            :href="`/catalog/${sub.slug}`"
                            class="listing__sub"
                        >
                            <span class="listing__sub-img img-ph" />
                            <span class="listing__sub-name">{{ sub.name }}</span>
                        </Link>
                    </div>

                    <div class="listing__head">
                        <h1 class="listing__title">{{ title }}</h1>
                        <span class="listing__count">Знайдено {{ pluralUa(products.total, 'товар', 'товари', 'товарів') }}</span>
                    </div>

                    <div v-if="category && brandsInScope.length" class="listing__brands">
                        <h2 class="listing__brands-title">Бренди</h2>
                        <div class="listing__brands-row">
                            <Link
                                v-for="brand in brandsInScope"
                                :key="brand.id"
                                :href="`${baseUrl}?brand[]=${brand.id}`"
                                class="listing__brand"
                            >{{ brand.name }}</Link>
                            <Link href="/brands" class="link-more listing__brands-all">Всі бренди</Link>
                        </div>
                    </div>

                    <div class="listing__toolbar">
                        <label class="listing__sort">
                            <span class="visually-hidden">Сортування</span>
                            <select :value="filters.sort" @change="changeSort">
                                <option value="popular">Найбільш популярні</option>
                                <option value="price_asc">Спочатку дешевші</option>
                                <option value="price_desc">Спочатку дорожчі</option>
                            </select>
                            <Icon name="chevron-down" :size="14" />
                        </label>
                        <div class="listing__view">
                            <button
                                class="listing__view-btn"
                                :class="{ 'listing__view-btn--active': viewMode === 'list' }"
                                type="button"
                                aria-label="Списком"
                                @click="viewMode = 'list'"
                            >
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect y="2" width="16" height="3"/><rect y="7" width="16" height="3"/><rect y="12" width="16" height="3"/></svg>
                            </button>
                            <button
                                class="listing__view-btn"
                                :class="{ 'listing__view-btn--active': viewMode === 'grid' }"
                                type="button"
                                aria-label="Плиткою"
                                @click="viewMode = 'grid'"
                            >
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect width="7" height="7"/><rect x="9" width="7" height="7"/><rect y="9" width="7" height="7"/><rect x="9" y="9" width="7" height="7"/></svg>
                            </button>
                        </div>
                    </div>

                    <div v-if="products.data.length" :class="viewMode === 'grid' ? 'listing__grid' : 'listing__list'">
                        <ProductCard v-for="product in products.data" :key="product.id" :product="product" :mode="viewMode" />
                    </div>

                    <div v-else class="listing__empty">
                        <p class="listing__empty-title">Нічого не знайдено</p>
                        <p>Спробуйте змінити або скинути фільтри.</p>
                    </div>

                    <nav v-if="products.last_page > 1" class="pagination" aria-label="Сторінки">
                        <template v-for="(link, i) in products.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="pagination__item"
                                :class="{ 'pagination__item--active': link.active }"
                                preserve-scroll
                            >{{ pageLabel(link, i) }}</Link>
                            <span v-else class="pagination__item pagination__item--disabled">{{ pageLabel(link, i) }}</span>
                        </template>
                    </nav>
                </main>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import ProductCard from '@/Components/ProductCard.vue';
import CatalogFilters from '@/Components/CatalogFilters.vue';
import { pluralUa } from '@/utils/format';

const props = defineProps({
    category:   Object,
    categories: Array,
    brands:     Array,
    products:   Object,
    filters:    Object,
    priceRange: Object,
});

const viewMode = ref('grid');

const title = computed(() => props.category?.name ?? (props.filters.q ? `Пошук: «${props.filters.q}»` : 'Усі товари'));
const baseUrl = computed(() => props.category ? `/catalog/${props.category.slug}` : '/catalog/all');
const brandsInScope = computed(() => props.brands.filter(b => b.count > 0));

// Laravel віддає «&laquo; Previous» / «Next &raquo;» — замінюємо на стрілки
function pageLabel(link, i) {
    if (i === 0) return '←';
    if (i === props.products.links.length - 1) return '→';
    return link.label;
}

function changeSort(e) {
    const url = new URL(window.location.href);
    url.searchParams.delete('page');
    if (e.target.value === 'popular') url.searchParams.delete('sort');
    else url.searchParams.set('sort', e.target.value);
    router.get(url.pathname + url.search, {}, { preserveScroll: true, preserveState: true });
}
</script>
