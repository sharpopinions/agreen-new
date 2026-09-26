<template>
    <aside class="filters">
        <h2 class="filters__title">Фільтри</h2>

        <div class="filters__group">
            <h3 class="filters__group-title filters__group-title--static">Ціна</h3>
            <div class="filters__price">
                <input v-model="minPrice" class="filters__price-input" type="number" min="0" :placeholder="fmt(priceRange.min)" aria-label="Ціна від" @change="apply" />
                <span class="filters__price-dash">—</span>
                <input v-model="maxPrice" class="filters__price-input" type="number" min="0" :placeholder="fmt(priceRange.max)" aria-label="Ціна до" @change="apply" />
            </div>
        </div>

        <FilterGroup v-if="categories.length" :title="categoryTitle">
            <label v-for="cat in categories" :key="cat.id" class="filters__check">
                <input type="checkbox" :value="cat.id" v-model="selected.category" @change="apply" />
                <span>{{ cat.name }} ({{ cat.count ?? 0 }})</span>
            </label>
        </FilterGroup>

        <FilterGroup v-if="visibleBrands.length" title="Бренди">
            <label v-for="brand in visibleBrands" :key="brand.id" class="filters__check">
                <input type="checkbox" :value="brand.id" v-model="selected.brand" @change="apply" />
                <span>{{ brand.name }} ({{ brand.count }})</span>
            </label>
        </FilterGroup>

        <FilterGroup title="Наявність">
            <label class="filters__check">
                <input type="checkbox" value="in_stock" v-model="selected.availability" @change="apply" />
                <span>В наявності</span>
            </label>
            <label class="filters__check">
                <input type="checkbox" value="preorder" v-model="selected.availability" @change="apply" />
                <span>Під замовлення</span>
            </label>
        </FilterGroup>

        <FilterGroup title="Акції">
            <label class="filters__check">
                <input type="checkbox" v-model="selected.sale" @change="apply" />
                <span>Показати лише акційні пропозиції</span>
            </label>
        </FilterGroup>

        <button v-if="hasActive" class="filters__reset" type="button" @click="reset">Скинути фільтри</button>
    </aside>
</template>

<script setup>
import { ref, reactive, computed, h } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    baseUrl:       { type: String, required: true },
    filters:       { type: Object, required: true },
    categories:    { type: Array, default: () => [] },
    categoryTitle: { type: String, default: 'Категорія' },
    brands:        { type: Array, default: () => [] },
    priceRange:    { type: Object, default: () => ({ min: 0, max: 0 }) },
});

const selected = reactive({
    brand:        [...props.filters.brand],
    category:     [...props.filters.category],
    availability: [...props.filters.availability],
    sale:         props.filters.sale,
});
const minPrice = ref(props.filters.min_price ?? '');
const maxPrice = ref(props.filters.max_price ?? '');

const visibleBrands = computed(() => props.brands.filter(b => b.count > 0 || selected.brand.includes(b.id)));

const hasActive = computed(() =>
    selected.brand.length || selected.category.length || selected.availability.length || selected.sale
    || minPrice.value !== '' || maxPrice.value !== '');

const fmt = (n) => Math.round(n).toLocaleString('uk-UA');

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
    apply();
}

// Група фільтра з розгортанням
const FilterGroup = {
    props: { title: String },
    setup(p, { slots }) {
        // На вузьких екранах групи згорнуті, щоб товари було видно одразу
        const open = ref(window.matchMedia('(min-width: 1025px)').matches);
        return () => h('div', { class: 'filters__group' }, [
            h('button', {
                class: 'filters__group-title',
                type: 'button',
                'aria-expanded': open.value,
                onClick: () => { open.value = !open.value; },
            }, [h(Icon, { name: open.value ? 'chevron-down' : 'chevron-right', size: 16, strokeWidth: 1.5 }), p.title]),
            open.value ? h('div', { class: 'filters__list' }, slots.default?.()) : null,
        ]);
    },
};
</script>
