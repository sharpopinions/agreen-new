<template>
    <AppLayout>
        <!-- Claude Design → extra-pages6.jsx: AktsyiaPage -->
        <div class="cat-page promo-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/sale" class="breadcrumbs__item">Акції</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ promotion.title }}</span>
            </nav>

            <header class="promo-hero">
                <div>
                    <div class="promo-card__tags promo-hero__tags">
                        <span v-if="promotion.discount" class="promo-tag promo-tag--discount">−{{ promotion.discount }}%</span>
                        <span v-if="promotion.until" class="promo-tag">До {{ promotion.until }}</span>
                    </div>
                    <h1 class="promo-hero__title">{{ promotion.title }}</h1>
                    <div v-if="promotion.description" class="rich-text promo-hero__text" v-html="promotion.description"></div>
                    <div class="promo-hero__facts">
                        <div v-if="promotion.brand">
                            <div class="promo-hero__fact-label">Бренд</div>
                            <Link :href="`/brands/${promotion.brand.slug}`" class="promo-hero__fact-value">{{ promotion.brand.name }}</Link>
                        </div>
                        <div v-if="promotion.category">
                            <div class="promo-hero__fact-label">Категорія</div>
                            <div class="promo-hero__fact-value">{{ promotion.category.name }}</div>
                        </div>
                        <div>
                            <div class="promo-hero__fact-label">Товарів</div>
                            <div class="promo-hero__fact-value">{{ promotion.productsCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="promo-hero__visual">
                    <img v-if="promotion.image || promotion.brand?.logo" :src="promotion.image || promotion.brand.logo" :alt="promotion.title" />
                    <template v-else>{{ (promotion.brand?.name || promotion.title).charAt(0) }}</template>
                </div>
            </header>

            <div class="cat-layout">
                <CatalogFilters
                    :key="base"
                    :base-url="base"
                    :filters="filters"
                    :categories="categories"
                    :brands="brands"
                    :price-range="priceRange"
                />
                <CatalogResults :base-url="base" :products="products" :filters="filters" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CatalogFilters from '@/Components/CatalogFilters.vue';
import CatalogResults from '@/Components/CatalogResults.vue';

const props = defineProps({
    promotion:  { type: Object, required: true },
    products:   Object,
    filters:    Object,
    categories: { type: Array, default: () => [] },
    brands:     { type: Array, default: () => [] },
    priceRange: Object,
});

const base = computed(() => `/sale/${props.promotion.slug}`);
</script>
