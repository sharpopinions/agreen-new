<template>
    <AppLayout>
        <!-- Claude Design → extra-pages8.jsx: BrandDetailPage, extra-pages3.jsx: BrandProductsPage -->
        <div class="cat-page brand-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/brands" class="breadcrumbs__item">Бренди</Link>
                <span class="breadcrumbs__sep">›</span>
                <template v-if="tab === 'products'">
                    <span class="breadcrumbs__item breadcrumbs__item--active">Продукція {{ brand.name }}</span>
                </template>
                <template v-else>
                    <Link :href="base" class="breadcrumbs__item">Продукція {{ brand.name }}</Link>
                    <span class="breadcrumbs__sep">›</span>
                    <span class="breadcrumbs__item breadcrumbs__item--active">{{ tab === 'about' ? 'Про бренд' : 'Медіабанк' }}</span>
                </template>
            </nav>

            <!-- Шапка бренду -->
            <header class="brand-hero" :class="{ 'brand-hero--stats': tab === 'products' && brand.stats.length }">
                <div class="brand-hero__logo">
                    <img v-if="brand.logo" :src="brand.logo" :alt="brand.name" />
                    <template v-else>{{ brand.name.charAt(0) }}</template>
                </div>
                <div>
                    <h1 class="brand-hero__title">{{ tab === 'products' ? `Продукція ${brand.name}` : brand.name }}</h1>
                    <p v-if="brand.shortDescription" class="brand-hero__text">{{ brand.shortDescription }}</p>
                </div>
                <div v-if="tab === 'products' && brand.stats.length" class="brand-hero__stats">
                    <div v-for="s in brand.stats.slice(0, 4)" :key="s.label" class="brand-hero__stat">
                        <div class="brand-hero__stat-value">{{ s.value }}</div>
                        <div class="brand-hero__stat-label">{{ s.label }}</div>
                    </div>
                </div>
                <Link v-else href="/partners" class="btn btn--outline btn--sm">Стати дилером →</Link>
            </header>

            <!-- Вкладки -->
            <nav class="brand-tabs">
                <Link :href="base" class="brand-tabs__item" :class="{ 'brand-tabs__item--active': tab === 'products' }" preserve-scroll>
                    Продукція <span class="brand-tabs__count">{{ brand.productsCount.toLocaleString('uk-UA') }}</span>
                </Link>
                <Link v-if="brand.hasAbout" :href="`${base}/about`" class="brand-tabs__item" :class="{ 'brand-tabs__item--active': tab === 'about' }" preserve-scroll>Про бренд</Link>
                <Link v-if="brand.hasMedia" :href="`${base}/media`" class="brand-tabs__item" :class="{ 'brand-tabs__item--active': tab === 'media' }" preserve-scroll>Медіабанк</Link>
            </nav>

            <!-- Продукція -->
            <div v-if="tab === 'products'" class="cat-layout">
                <CatalogFilters
                    :key="base"
                    :base-url="base"
                    :filters="filters"
                    :categories="categories"
                    category-title="Категорія"
                    :brands="[]"
                    :price-range="priceRange"
                    :attribute-filters="attributeFilters"
                />
                <CatalogResults :base-url="base" :products="products" :filters="filters" />
            </div>

            <!-- Про бренд -->
            <div v-else-if="tab === 'about'" class="brand-about">
                <div>
                    <div v-if="about" class="rich-text brand-about__text" v-html="about"></div>
                    <template v-if="otherBrands.length">
                        <h3 class="brand-about__subtitle">Інші бренди</h3>
                        <div class="brand-about__others">
                            <Link v-for="b in otherBrands" :key="b.slug" :href="`/brands/${b.slug}`" class="brand-chip">{{ b.name }}</Link>
                        </div>
                    </template>
                </div>
                <div v-if="brand.stats.length" class="brand-about__stats">
                    <div v-for="s in brand.stats" :key="s.label" class="brand-stat-row">
                        <span class="brand-stat-row__label">{{ s.label }}</span>
                        <span class="brand-stat-row__value">{{ s.value }}</span>
                    </div>
                </div>
            </div>

            <!-- Медіабанк -->
            <div v-else-if="tab === 'media'" class="brand-media">
                <div v-for="v in media.videos" :key="v.id" class="brand-media__video">
                    <iframe
                        :src="`https://www.youtube-nocookie.com/embed/${v.id}`"
                        :title="v.title"
                        loading="lazy"
                        allow="accelerometer; encrypted-media; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>

                <template v-if="media.images.length">
                    <h3 class="brand-about__subtitle">Медіа-матеріали ({{ media.images.length }})</h3>
                    <div class="brand-media__grid">
                        <a v-for="m in media.images" :key="m.id" :href="m.url" class="media-card" download>
                            <img :src="m.url" :alt="m.title" loading="lazy" class="media-card__img" />
                            <span class="media-card__body">
                                <span class="media-card__title">{{ m.title }}</span>
                                <span class="media-card__meta">
                                    <span>{{ [m.ext, m.size].filter(Boolean).join(' · ') }}</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                </span>
                            </span>
                        </a>
                    </div>
                </template>

                <template v-if="media.documents.length">
                    <h3 class="brand-about__subtitle">Друковані матеріали ({{ media.documents.length }})</h3>
                    <div class="doc-list">
                        <div v-for="d in media.documents" :key="d.id" class="doc-list__row">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="doc-list__icon">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                            </svg>
                            <div>
                                <div class="doc-list__title">{{ d.title }}</div>
                                <div class="doc-list__meta">{{ [d.ext, d.size, d.date].filter(Boolean).join(' · ') }}</div>
                            </div>
                            <a :href="d.url" class="btn btn--outline btn--sm" download>Завантажити</a>
                        </div>
                    </div>
                </template>
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
    brand:            { type: Object, required: true },
    tab:              { type: String, default: 'products' },
    // Продукція
    products:         Object,
    filters:          Object,
    categories:       { type: Array, default: () => [] },
    priceRange:       Object,
    attributeFilters: { type: Array, default: () => [] },
    // Про бренд
    about:            { type: String, default: null },
    otherBrands:      { type: Array, default: () => [] },
    // Медіабанк
    media:            { type: Object, default: () => ({ videos: [], images: [], documents: [] }) },
});

const base = computed(() => `/brands/${props.brand.slug}`);
</script>
