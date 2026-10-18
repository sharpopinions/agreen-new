<template>
    <AppLayout>
        <!-- Claude Design → extra-pages2.jsx: ComparePage -->
        <div class="info-page lists-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Порівняння товарів</span>
            </nav>

            <template v-if="products.length">
                <div class="lists-page__head">
                    <h1 class="info-page__title lists-page__title">Порівняння товарів</h1>
                    <div class="compare-controls">
                        <label class="compare-controls__label">
                            Показувати:
                            <select v-model="onlyDiff" class="compare-controls__select">
                                <option :value="false">Всі характеристики</option>
                                <option :value="true">Тільки відмінності</option>
                            </select>
                        </label>
                        <button type="button" class="btn btn--ghost btn--sm" @click="clear">Очистити</button>
                    </div>
                </div>

                <div v-if="tabs.length > 1" class="pill-tabs">
                    <Link
                        v-for="t in tabs"
                        :key="t.id"
                        :href="`/compare?category=${t.id}`"
                        class="pill-tabs__item"
                        :class="{ 'pill-tabs__item--active': t.id === category }"
                        preserve-scroll
                    >{{ t.name }} · {{ t.count }}</Link>
                </div>

                <div class="compare-scroll">
                    <table class="compare" :style="{ '--cols': columns }">
                        <thead>
                            <tr>
                                <th class="compare__corner" scope="col"><span class="visually-hidden">Характеристика</span></th>
                                <th v-for="p in products" :key="p.id" class="compare__product" scope="col">
                                    <button type="button" class="compare__remove" :aria-label="`Прибрати ${p.name} з порівняння`" @click="toggleCompare(p.id)">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                                    </button>
                                    <Link :href="`/p/${p.slug}`" class="compare__img">
                                        <img v-if="p.image" :src="p.image" :alt="p.name" />
                                        <ImgPlaceholder v-else :h="120" label="фото" :seed="p.sku" />
                                    </Link>
                                    <div class="compare__sku">Арт: {{ p.sku }}</div>
                                    <Link :href="`/p/${p.slug}`" class="compare__name">{{ p.name }}</Link>
                                    <div class="compare__price">
                                        {{ fmt(p.price) }}
                                        <span v-if="p.oldPrice" class="compare__old">{{ fmt(p.oldPrice) }}</span>
                                    </div>
                                    <button type="button" class="btn btn--primary btn--sm btn--full" :disabled="pending" @click="add(p.id)">До кошика</button>
                                </th>
                                <th v-if="products.length < limit" class="compare__add" scope="col">
                                    <Link href="/catalog" class="compare__add-link">
                                        <span class="compare__add-plus">+</span>
                                        Додати товар
                                    </Link>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="compare__section"><td :colspan="columns">Характеристики</td></tr>
                            <tr v-for="row in visibleRows" :key="row.name" :class="{ 'compare__row--diff': isDiff(row) }">
                                <th scope="row" class="compare__label">{{ row.name }}</th>
                                <td v-for="(v, i) in row.values" :key="i" class="compare__value">{{ v }}</td>
                                <td v-if="products.length < limit" class="compare__value"></td>
                            </tr>
                            <tr v-if="!visibleRows.length">
                                <td :colspan="columns" class="compare__none">Характеристики товарів однакові</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-if="products.length === 1" class="lists-page__hint">Додайте ще товар з цієї категорії, щоб порівняти характеристики.</p>
            </template>

            <div v-else class="lists-empty">
                <div class="lists-empty__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18" /></svg>
                </div>
                <h1 class="lists-empty__title">Порівняння товарів</h1>
                <p class="lists-empty__text">Додайте товари для порівняння — кнопка ⚖ на сторінці товару</p>
                <Link href="/catalog" class="btn btn--primary">До каталогу</Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import { useCart } from '@/composables/useCart';
import { useLists } from '@/composables/useLists';
import { formatPrice as fmt } from '@/utils/format';

const props = defineProps({
    tabs:     { type: Array, default: () => [] },
    category: { type: Number, default: null },
    products: { type: Array, default: () => [] },
    rows:     { type: Array, default: () => [] },
    limit:    { type: Number, default: 4 },
});

const { add, pending } = useCart();
const { toggleCompare } = useLists();
const onlyDiff = ref(false);

const columns = computed(() => props.products.length + 1 + (props.products.length < props.limit ? 1 : 0));
const isDiff = row => new Set(row.values).size > 1;
const visibleRows = computed(() => (onlyDiff.value ? props.rows.filter(isDiff) : props.rows));

function clear() {
    if (confirm('Очистити порівняння в цій категорії?')) {
        router.delete('/compare', { data: { category: props.category } });
    }
}
</script>
