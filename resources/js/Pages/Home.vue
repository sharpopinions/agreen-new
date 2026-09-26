<template>
    <AppLayout>
        <div class="container">

            <!-- Hero: категорії · банер · партнерство -->
            <section class="home-hero">
                <nav class="home-hero__cats" :class="{ 'home-hero__cats--open': showAllHeroCats }">
                    <Link
                        v-for="cat in heroCats"
                        :key="cat.id"
                        :href="`/catalog/${cat.slug}`"
                        class="home-hero__cat"
                    >
                        <Icon name="image" :size="20" :stroke-width="1" class="home-hero__cat-icon" />
                        <span class="home-hero__cat-name">{{ cat.name }}</span>
                        <Icon name="chevron-right" :size="18" :stroke-width="1" />
                    </Link>
                    <button
                        v-if="categories.length > heroLimit"
                        class="home-hero__cats-toggle"
                        type="button"
                        :aria-label="showAllHeroCats ? 'Згорнути' : 'Усі категорії'"
                        @click="showAllHeroCats = !showAllHeroCats"
                    >
                        <Icon :name="showAllHeroCats ? 'chevron-up' : 'chevron-down'" :size="24" :stroke-width="1" />
                    </button>
                </nav>

                <div class="home-hero__banner">
                    <button class="home-hero__arrow home-hero__arrow--prev" type="button" aria-label="Попередній слайд" @click="prevSlide">
                        <Icon name="chevron-left" :size="40" :stroke-width="1" />
                    </button>
                    <div class="home-hero__slide">
                        <p class="home-hero__text">{{ slide.text }}</p>
                        <Link :href="slide.cta.href" class="btn btn--primary home-hero__cta">{{ slide.cta.label }}</Link>
                    </div>
                    <button class="home-hero__arrow home-hero__arrow--next" type="button" aria-label="Наступний слайд" @click="nextSlide">
                        <Icon name="chevron-right" :size="40" :stroke-width="1" />
                    </button>
                    <div class="home-hero__dots">
                        <button
                            v-for="(s, i) in heroSlides"
                            :key="i"
                            type="button"
                            class="home-hero__dot"
                            :class="{ 'home-hero__dot--active': i === slideIndex }"
                            :aria-label="`Слайд ${i + 1}`"
                            @click="slideIndex = i"
                        />
                    </div>
                </div>

                <aside class="home-hero__partner">
                    <h2 class="home-hero__partner-title">Партнерство</h2>
                    <div class="home-hero__partner-img img-ph" />
                    <p class="home-hero__partner-text">
                        Вигідні умови для дилерів та оптових покупців. Гнучка система знижок і персональний менеджер.
                    </p>
                    <Link href="/partners" class="link-more home-hero__partner-link">Стати партнером</Link>
                </aside>
            </section>

            <!-- Бренди -->
            <section class="section">
                <h2 class="section__title">Бренди</h2>
                <div class="home-brands">
                    <Link
                        v-for="brand in brands.slice(0, 5)"
                        :key="brand.id"
                        href="/brands"
                        class="home-brands__item"
                    >{{ brand.name }}</Link>
                </div>
                <div class="section__more">
                    <Link href="/brands" class="btn btn--primary btn--wide">Дивитись усі бренди</Link>
                </div>
            </section>

            <!-- Популярні категорії -->
            <section class="section">
                <h2 class="section__title">Популярні категорії</h2>
                <div class="home-categories">
                    <CategoryCard
                        v-for="cat in visibleCategories"
                        :key="cat.id"
                        :category="cat"
                    />
                </div>
                <div v-if="categories.length > 6 && !showAllCategories" class="section__more">
                    <button class="btn btn--primary btn--wide" type="button" @click="showAllCategories = true">
                        Показати ще
                    </button>
                </div>
            </section>

            <!-- Каталог з табами -->
            <section class="section">
                <h2 class="section__title">Каталог</h2>
                <div class="home-tabs" role="tablist">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        class="home-tabs__tab"
                        :class="{ 'home-tabs__tab--active': activeTab === tab.key }"
                        role="tab"
                        :aria-selected="activeTab === tab.key"
                        type="button"
                        @click="activeTab = tab.key"
                    >{{ tab.label }}</button>
                </div>
                <div v-if="filteredProducts.length" class="product-grid">
                    <ProductCard
                        v-for="product in filteredProducts.slice(0, 8)"
                        :key="product.id"
                        :product="product"
                    />
                </div>
                <p v-else class="home-tabs__empty">Товарів у цій добірці поки немає.</p>
                <div class="section__more">
                    <Link href="/catalog" class="btn btn--primary btn--wide">Перейти в каталог</Link>
                </div>
            </section>

            <!-- Послуги -->
            <section class="section">
                <h2 class="section__title">Послуги</h2>
                <div class="home-services">
                    <article v-for="service in services" :key="service.title" class="home-services__card">
                        <div class="home-services__head">
                            <Icon name="image" :size="40" :stroke-width="0.75" />
                            <h3 class="home-services__title">{{ service.title }}</h3>
                        </div>
                        <p class="home-services__text">{{ service.text }}</p>
                        <Link :href="service.href" class="link-more">Детальніше</Link>
                    </article>
                </div>
                <div class="section__more">
                    <Link href="/services" class="btn btn--primary btn--wide">Дивитись усі послуги</Link>
                </div>
            </section>

            <!-- Партнерство -->
            <section class="section">
                <h2 class="section__title">Партнерство</h2>
                <div class="home-partners">
                    <article v-for="offer in partnerOffers" :key="offer.title" class="home-partners__item">
                        <Icon name="image" :size="40" :stroke-width="0.75" class="home-partners__icon" />
                        <div>
                            <h3 class="home-partners__title">{{ offer.title }}</h3>
                            <p class="home-partners__text">{{ offer.text }}</p>
                        </div>
                    </article>
                </div>
            </section>
        </div>

        <section class="home-dealer">
            <div class="home-dealer__inner container">
                <div>
                    <h3 class="home-dealer__title">{{ dealerBanner.title }}</h3>
                    <p class="home-dealer__text">{{ dealerBanner.text }}</p>
                </div>
                <div>
                    <p class="home-dealer__list-title">{{ dealerBanner.listTitle }}</p>
                    <ul class="home-dealer__list">
                        <li v-for="item in dealerBanner.list" :key="item">{{ item }}</li>
                    </ul>
                </div>
                <Link :href="dealerBanner.cta.href" class="btn btn--primary btn--wide">{{ dealerBanner.cta.label }}</Link>
            </div>
        </section>

        <div class="container">
            <!-- Про компанію -->
            <section class="section">
                <h2 class="section__title">Про компанію</h2>
                <div class="home-stats">
                    <div v-for="stat in aboutStats" :key="stat.label" class="home-stats__item">
                        <div class="home-stats__value">{{ stat.value }}</div>
                        <div class="home-stats__label">{{ stat.label }}</div>
                    </div>
                </div>
                <div class="home-about">
                    <div class="home-about__img img-ph" />
                    <div class="home-about__body">
                        <p class="home-about__lead">{{ aboutText.lead }}</p>
                        <p class="home-about__text">{{ aboutText.body }}</p>
                        <Link href="/about" class="link-more">Детальніше</Link>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import ProductCard from '@/Components/ProductCard.vue';
import CategoryCard from '@/Components/CategoryCard.vue';
import { heroSlides, services, partnerOffers, dealerBanner, aboutStats, aboutText } from '@/data/home';

const props = defineProps({
    categories: Array,
    brands:     Array,
    products:   Array,
});

// Hero
const heroLimit = 11;
const showAllHeroCats = ref(false);
const heroCats = computed(() => showAllHeroCats.value ? props.categories : props.categories.slice(0, heroLimit));

const slideIndex = ref(0);
const slide = computed(() => heroSlides[slideIndex.value]);
const nextSlide = () => { slideIndex.value = (slideIndex.value + 1) % heroSlides.length; };
const prevSlide = () => { slideIndex.value = (slideIndex.value - 1 + heroSlides.length) % heroSlides.length; };

// Категорії
const showAllCategories = ref(false);
const visibleCategories = computed(() => showAllCategories.value ? props.categories : props.categories.slice(0, 6));

// Таби каталогу
const activeTab = ref('popular');

const tabs = [
    { key: 'popular',     label: 'Популярні товари' },
    { key: 'recommended', label: 'Рекомендовані'    },
    { key: 'sale',        label: 'Акційні'          },
    { key: 'preorder',    label: 'Передзамовлення'  },
];

const filteredProducts = computed(() => {
    switch (activeTab.value) {
        case 'recommended': return props.products.filter(p => p.badge?.name === 'Хіт');
        case 'sale':        return props.products.filter(p => p.oldPrice || p.badge?.name === 'Акція');
        case 'preorder':    return props.products.filter(p => p.stock === 0);
        default:            return props.products;
    }
});
</script>
