<template>
    <AppLayout>
        <!-- Claude Design → extra-pages4.jsx: AktsyiiPage -->
        <div class="info-page promos-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Акції</span>
            </nav>

            <div class="promos-page__banner">
                <ImgPlaceholder :h="200" label="банер акцій — знижки та спецпропозиції" seed="sale-banner" />
            </div>

            <div class="promos-page__head">
                <h1 class="info-page__title promos-page__title">Акції</h1>
                <span class="promos-page__count">Актуальних пропозицій: <strong>{{ filtered.length }}</strong></span>
            </div>
            <p class="promos-page__subtitle">Спеціальні пропозиції та знижки для наших клієнтів</p>

            <div class="promos-page__layout">
                <aside class="cat-filter promos-filter">
                    <div class="cat-filter__header">
                        <span class="cat-filter__title">Фільтри</span>
                        <button v-if="cat || brand" class="cat-filter__reset" @click="cat = brand = null">Скинути</button>
                    </div>
                    <div v-if="categories.length" class="cat-filter__group">
                        <div class="cat-filter__group-btn cat-filter__group-btn--static"><span>Категорія</span></div>
                        <div class="cat-filter__body"><ul class="cat-filter__list">
                            <li v-for="c in categories" :key="c.id">
                                <label class="cat-filter__check">
                                    <input type="checkbox" :checked="cat === c.id" @change="cat = cat === c.id ? null : c.id" />
                                    <span class="cat-filter__check-label">{{ c.name }}</span>
                                </label>
                            </li>
                        </ul></div>
                    </div>
                    <div v-if="brands.length" class="cat-filter__group">
                        <div class="cat-filter__group-btn cat-filter__group-btn--static"><span>Бренд</span></div>
                        <div class="cat-filter__body"><ul class="cat-filter__list">
                            <li v-for="b in brands" :key="b.id">
                                <label class="cat-filter__check">
                                    <input type="checkbox" :checked="brand === b.id" @change="brand = brand === b.id ? null : b.id" />
                                    <span class="cat-filter__check-label">{{ b.name }}</span>
                                </label>
                            </li>
                        </ul></div>
                    </div>
                </aside>

                <div class="promos-list">
                    <Link v-for="p in filtered" :key="p.id" :href="`/sale/${p.slug}`" class="promo-card">
                        <span class="promo-card__brand">
                            <span class="promo-card__logo">
                                <img v-if="p.image || p.brand?.logo" :src="p.image || p.brand.logo" :alt="p.brand?.name || p.title" loading="lazy" />
                                <template v-else>{{ (p.brand?.name || p.title).charAt(0) }}</template>
                            </span>
                            <span v-if="p.brand" class="promo-card__brand-name">{{ p.brand.name }}</span>
                        </span>
                        <span class="promo-card__info">
                            <span class="promo-card__tags">
                                <span v-if="p.discount" class="promo-tag promo-tag--discount">−{{ p.discount }}%</span>
                                <span v-if="p.category" class="promo-tag">{{ p.category.name }}</span>
                            </span>
                            <span class="promo-card__title">{{ p.title }}</span>
                            <span v-if="p.until" class="promo-card__until">До {{ p.until }}</span>
                        </span>
                        <span class="promo-card__cta"><span class="btn btn--primary btn--sm">Переглянути →</span></span>
                    </Link>

                    <div v-if="!filtered.length" class="promos-list__empty">
                        <p>{{ promotions.length ? 'Акцій за вибраними фільтрами не знайдено' : 'Зараз немає активних акцій — загляньте трохи згодом.' }}</p>
                        <button v-if="cat || brand" class="btn btn--outline btn--sm" @click="cat = brand = null">Скинути фільтри</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';

const props = defineProps({
    promotions: { type: Array, default: () => [] },
});

const cat   = ref(null);
const brand = ref(null);

const uniq = (items) => [...new Map(items.filter(Boolean).map(i => [i.id, i])).values()];
const categories = computed(() => uniq(props.promotions.map(p => p.category)));
const brands     = computed(() => uniq(props.promotions.map(p => p.brand)));

const filtered = computed(() => props.promotions.filter(p =>
    (!cat.value || p.category?.id === cat.value) && (!brand.value || p.brand?.id === brand.value)));
</script>
