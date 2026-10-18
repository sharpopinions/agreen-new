<template>
    <AppLayout>
        <!-- Claude Design → extra-pages.jsx: FavoritesPage -->
        <div class="info-page lists-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Обрані товари</span>
            </nav>

            <div v-if="products.length">
                <div class="lists-page__head">
                    <h1 class="info-page__title lists-page__title">Обрані товари</h1>
                    <span class="lists-page__count">{{ pluralUa(products.length, 'товар', 'товари', 'товарів') }}</span>
                </div>

                <div class="lists-grid">
                    <ProductCard v-for="p in products" :key="p.id" :product="p" />
                </div>

                <div class="lists-bar">
                    <span class="lists-bar__total">Всього: {{ pluralUa(products.length, 'товар', 'товари', 'товарів') }} на {{ fmt(total) }}</span>
                    <div class="lists-bar__actions">
                        <button type="button" class="btn btn--outline" @click="clear">Очистити список</button>
                        <button type="button" class="btn btn--primary" :disabled="busy" @click="toCart">Додати все до кошика</button>
                    </div>
                </div>
            </div>

            <div v-else class="lists-empty">
                <div class="lists-empty__icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" /></svg>
                </div>
                <h1 class="lists-empty__title">Список бажань порожній</h1>
                <p class="lists-empty__text">Додайте товари з каталогу — натисніть ♡ на картці товару</p>
                <Link href="/catalog" class="btn btn--primary">До каталогу</Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { formatPrice as fmt, pluralUa } from '@/utils/format';

const props = defineProps({
    products: { type: Array, default: () => [] },
});

const busy = ref(false);
const total = computed(() => props.products.reduce((sum, p) => sum + p.price, 0));

function clear() {
    if (confirm('Очистити список обраних товарів?')) {
        router.delete('/wishlist', { preserveScroll: true });
    }
}

function toCart() {
    router.post('/wishlist/to-cart', {}, {
        onStart: () => (busy.value = true),
        onFinish: () => (busy.value = false),
    });
}
</script>
