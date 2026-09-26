<template>
    <aside class="cat-sidebar">
        <div class="cat-filter">

            <div class="cat-filter__header">
                <span class="cat-filter__title">Фільтри</span>
                <button class="cat-filter__reset" @click="reset">Очистити всі</button>
            </div>

            <!-- Ціна -->
            <div class="cat-filter__group">
                <button class="cat-filter__group-btn" @click="toggle('price')">
                    <span>Ціна</span>
                    <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': open.price }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div v-show="open.price" class="cat-filter__body">
                    <div class="cat-filter__price-row">
                        <input class="cat-filter__price-input" type="number" :placeholder="`від ${fmt(priceRange.min)}`" v-model="minPrice" min="0" />
                        <span class="cat-filter__price-dash">—</span>
                        <input class="cat-filter__price-input" type="number" :placeholder="`до ${fmt(priceRange.max)}`" v-model="maxPrice" min="0" />
                    </div>
                    <button class="cat-filter__apply-btn" @click="apply">Застосувати</button>
                </div>
            </div>

            <!-- Категорія / Підкатегорія -->
            <div v-if="categories.length" class="cat-filter__group">
                <button class="cat-filter__group-btn" @click="toggle('category')">
                    <span>{{ categoryTitle }}</span>
                    <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': open.category }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div v-show="open.category" class="cat-filter__body">
                    <ul class="cat-filter__list">
                        <li v-for="cat in categories" :key="cat.id">
                            <label class="cat-filter__check">
                                <input type="checkbox" :value="cat.id" v-model="selected.category" @change="apply" />
                                <span class="cat-filter__check-label">{{ cat.name }}</span>
                                <span class="cat-filter__check-count">{{ (cat.count ?? 0).toLocaleString('uk-UA') }}</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Бренди -->
            <div v-if="visibleBrands.length" class="cat-filter__group">
                <button class="cat-filter__group-btn" @click="toggle('brands')">
                    <span>Бренди</span>
                    <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': open.brands }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div v-show="open.brands" class="cat-filter__body">
                    <ul class="cat-filter__list">
                        <li v-for="brand in visibleBrands" :key="brand.id">
                            <label class="cat-filter__check">
                                <input type="checkbox" :value="brand.id" v-model="selected.brand" @change="apply" />
                                <span class="cat-filter__check-label">{{ brand.name }}</span>
                                <span class="cat-filter__check-count">{{ brand.count }}</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Наявність -->
            <div class="cat-filter__group">
                <button class="cat-filter__group-btn" @click="toggle('availability')">
                    <span>Наявність</span>
                    <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': open.availability }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div v-show="open.availability" class="cat-filter__body">
                    <ul class="cat-filter__list">
                        <li>
                            <label class="cat-filter__check">
                                <input type="checkbox" value="in_stock" v-model="selected.availability" @change="apply" />
                                <span class="cat-filter__check-label">В наявності</span>
                            </label>
                        </li>
                        <li>
                            <label class="cat-filter__check">
                                <input type="checkbox" value="preorder" v-model="selected.availability" @change="apply" />
                                <span class="cat-filter__check-label">Під замовлення</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Акції -->
            <div class="cat-filter__group">
                <button class="cat-filter__group-btn" @click="toggle('sale')">
                    <span>Акції</span>
                    <svg :class="['cat-filter__chevron', { 'cat-filter__chevron--up': open.sale }]" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div v-show="open.sale" class="cat-filter__body">
                    <label class="cat-filter__check">
                        <input type="checkbox" v-model="selected.sale" @change="apply" />
                        <span class="cat-filter__check-label">Лише акційні пропозиції</span>
                    </label>
                </div>
            </div>

        </div>
    </aside>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    baseUrl:       { type: String, required: true },
    filters:       { type: Object, required: true },
    categories:    { type: Array, default: () => [] },
    categoryTitle: { type: String, default: 'Категорія' },
    brands:        { type: Array, default: () => [] },
    priceRange:    { type: Object, default: () => ({ min: 0, max: 0 }) },
});

const open = ref({ price: true, category: true, brands: true, availability: false, sale: false });
const toggle = (key) => { open.value[key] = !open.value[key]; };

const selected = reactive({
    brand:        [...props.filters.brand],
    category:     [...props.filters.category],
    availability: [...props.filters.availability],
    sale:         props.filters.sale,
});
const minPrice = ref(props.filters.min_price ?? '');
const maxPrice = ref(props.filters.max_price ?? '');

// Показуємо бренди, що є у вибірці, плюс уже вибрані
const visibleBrands = computed(() => props.brands.filter(b => b.count > 0 || selected.brand.includes(b.id)));

const fmt = (n) => Math.round(n ?? 0).toLocaleString('uk-UA');

function query() {
    return {
        q:            props.filters.q || undefined,
        sort:         props.filters.sort !== 'popular' ? props.filters.sort : undefined,
        brand:        selected.brand.length ? selected.brand : undefined,
        category:     selected.category.length ? selected.category : undefined,
        availability: selected.availability.length ? selected.availability : undefined,
        sale:         selected.sale ? 1 : undefined,
        min_price:    minPrice.value !== '' ? minPrice.value : undefined,
        max_price:    maxPrice.value !== '' ? maxPrice.value : undefined,
    };
}

function apply() {
    router.get(props.baseUrl, query(), { preserveScroll: true, preserveState: true });
}

function reset() {
    Object.assign(selected, { brand: [], category: [], availability: [], sale: false });
    minPrice.value = '';
    maxPrice.value = '';
    router.get(props.baseUrl, {}, { preserveScroll: true, preserveState: true });
}
</script>
