<template>
    <AppLayout>
        <div class="cat-page">

            <!-- Хлібні крихти -->
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Каталог</span>
            </nav>

            <!-- Заголовок -->
            <h1 class="cat-page__title">{{ category.name }}</h1>
            <p class="cat-page__subtitle">Оберіть категорію або скористайтесь пошуком. Для партнерів — гнучка система знижок!</p>

            <!-- Пошук -->
            <div class="cat-page__search-wrap">
                <svg class="cat-page__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input class="cat-page__search" type="text" placeholder="Введіть назву товару або категорії..." />
            </div>

            <!-- Основний лейаут -->
            <div class="cat-layout">

                <!-- Сайдбар -->
                <aside class="cat-sidebar">
                    <div class="cat-filter">

                        <div class="cat-filter__header">
                            <span class="cat-filter__title">Фільтри</span>
                            <button class="cat-filter__reset" @click="resetAll">Очистити всі</button>
                        </div>

                        <!-- Ціна -->
                        <div class="cat-filter__group">
                            <button class="cat-filter__group-btn" @click="toggleSection('price')">
                                <span>Ціна</span>
                                <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': sections.price }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div v-show="sections.price" class="cat-filter__body">
                                <div class="cat-filter__price-row">
                                    <input class="cat-filter__price-input" type="number" placeholder="від" v-model="localMinPrice" min="0" />
                                    <span class="cat-filter__price-dash">—</span>
                                    <input class="cat-filter__price-input" type="number" placeholder="до" v-model="localMaxPrice" min="0" />
                                </div>
                                <input
                                    class="cat-filter__range"
                                    type="range"
                                    min="0"
                                    max="5000"
                                    :value="localMaxPrice || 5000"
                                    @input="localMaxPrice = $event.target.value"
                                />
                                <div class="cat-filter__range-labels">
                                    <span>0 ₴</span>
                                    <span>5 000 ₴</span>
                                </div>
                                <button class="cat-filter__apply-btn" @click="applyPrice">Застосувати</button>
                            </div>
                        </div>

                        <!-- Категорія -->
                        <div class="cat-filter__group">
                            <button class="cat-filter__group-btn" @click="toggleSection('category')">
                                <span>Категорія</span>
                                <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': sections.category }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div v-show="sections.category" class="cat-filter__body">
                                <ul class="cat-filter__list">
                                    <li v-for="cat in categories" :key="cat.id">
                                        <label class="cat-filter__check">
                                            <input
                                                type="checkbox"
                                                :checked="category.slug === cat.slug"
                                                @change="goToCategory(cat.slug)"
                                            />
                                            <span class="cat-filter__check-label">{{ cat.name }}</span>
                                            <span class="cat-filter__check-count">{{ (cat.count ?? 0).toLocaleString('uk-UA') }}</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Бренди -->
                        <div class="cat-filter__group">
                            <button class="cat-filter__group-btn" @click="toggleSection('brands')">
                                <span>Бренди</span>
                                <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': sections.brands }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div v-show="sections.brands" class="cat-filter__body">
                                <ul class="cat-filter__list">
                                    <li v-for="brand in brands" :key="brand.id">
                                        <label class="cat-filter__check">
                                            <input
                                                type="checkbox"
                                                :checked="activeBrands.includes(brand.id)"
                                                @change="toggleBrand(brand.id)"
                                            />
                                            <span class="cat-filter__check-label">{{ brand.name }}</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Наявність -->
                        <div class="cat-filter__group">
                            <button class="cat-filter__group-btn" @click="toggleSection('availability')">
                                <span>Наявність</span>
                                <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': sections.availability }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                        </div>

                    </div>
                </aside>

                <!-- Основний контент -->
                <main class="cat-main">

                    <!-- Тулбар -->
                    <div class="cat-toolbar">
                        <span class="cat-toolbar__count">
                            Знайдено: <strong>{{ pluralUa(products.total, 'товар', 'товари', 'товарів') }}</strong>
                        </span>
                        <div class="cat-toolbar__right">
                            <select class="cat-toolbar__sort" :value="currentSort" @change="changeSort">
                                <option value="default">Популярні</option>
                                <option value="price_asc">Ціна: від низької</option>
                                <option value="price_desc">Ціна: від високої</option>
                            </select>
                            <div class="cat-toolbar__view">
                                <button
                                    class="cat-toolbar__view-btn"
                                    :class="{ 'cat-toolbar__view-btn--active': viewMode === 'grid' }"
                                    @click="viewMode = 'grid'"
                                    aria-label="Плитка"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/>
                                        <rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/>
                                    </svg>
                                </button>
                                <button
                                    class="cat-toolbar__view-btn"
                                    :class="{ 'cat-toolbar__view-btn--active': viewMode === 'list' }"
                                    @click="viewMode = 'list'"
                                    aria-label="Список"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                                        <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Товари або порожній стан -->
                    <div v-if="products.data.length" :class="viewMode === 'grid' ? 'cat-grid' : 'cat-list'">
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                            :mode="viewMode"
                        />
                    </div>

                    <div v-else class="cat-empty">
                        <div class="cat-empty__icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <p class="cat-empty__title">Нічого не знайдено</p>
                        <p class="cat-empty__text">Спробуйте змінити фільтри</p>
                        <button class="cat-empty__btn" @click="resetAll">Скинути фільтри</button>
                    </div>

                    <!-- Пагінація -->
                    <div v-if="products.last_page > 1" class="cat-pagination">
                        <button
                            v-for="page in products.last_page"
                            :key="page"
                            class="cat-pagination__btn"
                            :class="{ 'cat-pagination__btn--active': page === products.current_page }"
                            @click="goToPage(page)"
                        >
                            {{ page }}
                        </button>
                    </div>

                </main>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    category:     Object,
    categories:   Array,
    brands:       Array,
    products:     Object,
    activeBrands: Array,
    currentSort:  String,
    minPrice:     Number,
    maxPrice:     Number,
});

const localMinPrice = ref(props.minPrice ?? '');
const localMaxPrice = ref(props.maxPrice ?? '');
const viewMode = ref('grid');

function pluralUa(n, one, few, many) {
    const m10 = n % 10, m100 = n % 100;
    if (m10 === 1 && m100 !== 11) return `${n} ${one}`;
    if (m10 >= 2 && m10 <= 4 && (m100 < 10 || m100 >= 20)) return `${n} ${few}`;
    return `${n} ${many}`;
}

const sections = ref({
    price:        true,
    category:     true,
    brands:       true,
    availability: false,
});

function toggleSection(key) {
    sections.value[key] = !sections.value[key];
}

function buildQuery() {
    return {
        brand:     props.activeBrands.length ? props.activeBrands : undefined,
        sort:      props.currentSort !== 'default' ? props.currentSort : undefined,
        min_price: localMinPrice.value || undefined,
        max_price: localMaxPrice.value || undefined,
    };
}

function goToCategory(slug) {
    router.get(`/catalog/${slug}`, {}, { preserveScroll: true });
}

function toggleBrand(id) {
    const brands = props.activeBrands.includes(id)
        ? props.activeBrands.filter(b => b !== id)
        : [...props.activeBrands, id];

    router.get(`/catalog/${props.category.slug}`, {
        ...buildQuery(),
        brand: brands,
    }, { preserveScroll: true });
}

function changeSort(e) {
    router.get(`/catalog/${props.category.slug}`, {
        ...buildQuery(),
        sort: e.target.value,
    }, { preserveScroll: true });
}

function applyPrice() {
    router.get(`/catalog/${props.category.slug}`, buildQuery(), { preserveScroll: true });
}

function resetAll() {
    localMinPrice.value = '';
    localMaxPrice.value = '';
    router.get(`/catalog/${props.category.slug}`, {}, { preserveScroll: true });
}

function goToPage(page) {
    router.get(`/catalog/${props.category.slug}`, {
        ...buildQuery(),
        page,
    }, { preserveScroll: true });
}
</script>
