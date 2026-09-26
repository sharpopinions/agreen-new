<template>
    <AppLayout>
        <div class="container">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <Link href="/catalog" class="breadcrumbs__item">Каталог</Link>
                <Link v-if="product.category" :href="`/catalog/${product.category.slug}`" class="breadcrumbs__item">
                    {{ product.category.name }}
                </Link>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ product.name }}</span>
            </nav>

            <nav class="product-anchors" aria-label="Розділи сторінки товару">
                <a v-for="a in anchors" :key="a.id" :href="`#${a.id}`" class="product-anchors__link">{{ a.label }}</a>
            </nav>

            <!-- Галерея + інформація -->
            <section id="all" class="product-top">
                <div class="product-gallery">
                    <div class="product-gallery__main">
                        <span v-if="product.badges.length" class="product-gallery__badge">{{ product.badges[0].name }}</span>
                        <button class="product-gallery__arrow product-gallery__arrow--prev" type="button" aria-label="Попереднє фото">
                            <Icon name="chevron-left" :size="40" :stroke-width="1" />
                        </button>
                        <button class="product-gallery__arrow product-gallery__arrow--next" type="button" aria-label="Наступне фото">
                            <Icon name="chevron-right" :size="40" :stroke-width="1" />
                        </button>
                    </div>
                    <div class="product-gallery__thumbs">
                        <span v-for="i in 4" :key="i" class="product-gallery__thumb" />
                    </div>
                </div>

                <div class="product-info">
                    <h1 class="product-info__name">{{ product.name }}</h1>

                    <div class="product-info__rating">
                        <Stars :value="product.rating" :size="24" />
                        <a href="#reviews" class="product-info__reviews">{{ product.rating || 0 }}/5 ({{ reviewsLabel }})</a>
                        <a href="#reviews" class="product-info__review-link">Залишити відгук</a>
                    </div>

                    <div class="product-info__meta">
                        <span v-if="product.brand" class="product-info__brand">
                            Бренд: <Link :href="`/brands/${product.brand.slug}`">{{ product.brand.name }}</Link>
                        </span>
                        <span class="product-info__sku">Артикул: {{ product.sku }}</span>
                    </div>

                    <div class="product-info__stock">
                        <span v-if="inStock" class="product-info__status product-info__status--ok">Є в наявності</span>
                        <span v-else class="product-info__status product-info__status--order">Під замовлення</span>
                        <span v-if="inStock" class="product-info__stock-qty">На складі: {{ product.stock }} шт</span>
                    </div>

                    <Qty v-model="qty" :size="26" class="product-info__qty" />

                    <div class="product-info__buy">
                        <div class="product-info__prices">
                            <s v-if="product.oldPrice" class="product-info__old-price">{{ fmt(product.oldPrice) }}</s>
                            <span class="product-info__price">{{ fmt(product.price) }}</span>
                        </div>
                        <button class="btn btn--primary product-info__btn" type="button">Додати в кошик</button>
                        <button class="btn btn--outline product-info__btn" type="button">Швидке замовлення</button>
                        <div class="product-info__tools">
                            <button class="product-card__tool" type="button" aria-label="Додати до порівняння"><Icon name="compare" :size="26" /></button>
                            <button class="product-card__tool" type="button" aria-label="Додати в обране"><Icon name="heart" :size="26" /></button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Опис -->
            <section id="description" class="product-section">
                <h2 class="product-section__title">Опис товару</h2>
                <div class="product-section__body">
                    <template v-if="product.description">
                        <div class="product-description" :class="{ 'product-description--open': descOpen }">{{ product.description }}</div>
                        <button v-if="product.description.length > 600" class="product-section__more" type="button" @click="descOpen = !descOpen">
                            {{ descOpen ? 'Згорнути' : 'Читати повністю' }}
                        </button>
                    </template>
                    <p v-else class="product-section__empty">Опис не заповнено.</p>
                </div>
            </section>

            <!-- Характеристики -->
            <section id="attributes" class="product-section">
                <h2 class="product-section__title">Характеристики</h2>
                <div class="product-section__body">
                    <ul v-if="product.attributes.length" class="product-attrs">
                        <li v-for="attr in product.attributes" :key="attr.name">{{ attr.name }}: {{ attr.value }}</li>
                    </ul>
                    <p v-else class="product-section__empty">Характеристики не заповнено.</p>
                </div>
            </section>

            <!-- Відгуки -->
            <section id="reviews" class="product-section">
                <div class="product-section__title-col">
                    <h2 class="product-section__title">Відгуки</h2>
                    <div class="product-rating">
                        <div class="product-rating__row">
                            <span>Оцінка користувачів</span>
                            <span>{{ product.rating || 0 }}/5</span>
                        </div>
                        <div class="product-rating__row">На основі {{ reviewsLabel }}</div>
                    </div>
                    <button class="btn btn--outline btn--full" type="button">Написати відгук</button>
                </div>
                <!-- Картки відгуків — після появи моделі відгуків (Figma: Product-card, блок «Відгуки») -->
                <div class="product-section__body" />
            </section>

            <!-- Супутні товари -->
            <section v-if="related.length" id="related" class="section">
                <h2 class="section__title section__title--left">Супутні товари</h2>
                <ProductCarousel :products="related" />
            </section>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import Stars from '@/Components/Stars.vue';
import Qty from '@/Components/Qty.vue';
import ProductCarousel from '@/Components/ProductCarousel.vue';
import { formatPrice as fmt, pluralUa } from '@/utils/format';

const props = defineProps({
    product: Object,
    related: Array,
});

const qty = ref(1);
const descOpen = ref(false);

const inStock = computed(() => (props.product.stock ?? 0) > 0);
const reviewsLabel = computed(() => pluralUa(props.product.reviews ?? 0, 'відгук', 'відгуки', 'відгуків'));

const anchors = computed(() => [
    { id: 'all',         label: 'Усе про товар'  },
    { id: 'description', label: 'Опис товару'    },
    { id: 'attributes',  label: 'Характеристики' },
    { id: 'reviews',     label: 'Відгуки'        },
    ...(props.related.length ? [{ id: 'related', label: 'Супутні товари' }] : []),
]);
</script>
