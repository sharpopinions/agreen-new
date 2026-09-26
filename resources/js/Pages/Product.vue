<template>
    <AppLayout>
        <div class="product-page">

            <!-- Breadcrumbs -->
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/catalog" class="breadcrumbs__item">Каталог</Link>
                <template v-if="product.category">
                    <span class="breadcrumbs__sep">›</span>
                    <Link :href="`/catalog/${product.category.slug}`" class="breadcrumbs__item">
                        {{ product.category.name }}
                    </Link>
                </template>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ product.name }}</span>
            </nav>

            <!-- Gallery + Info -->
            <div class="product-page__body">

                <!-- Gallery -->
                <div class="product-gallery">
                    <div class="product-gallery__main">
                        <span class="product-gallery__label">ФОТО ТОВАРУ</span>
                    </div>
                    <div class="product-gallery__thumbs">
                        <div v-for="i in 4" :key="i" class="product-gallery__thumb"></div>
                    </div>
                </div>

                <!-- Info -->
                <div class="product-info">

                    <div v-if="product.badges.length" class="product-info__badges">
                        <span
                            v-for="b in product.badges"
                            :key="b.name"
                            class="product-card__badge"
                            :style="{ color: b.color, backgroundColor: b.bgColor }"
                        >{{ b.name }}</span>
                    </div>

                    <h1 class="product-info__name">{{ product.name }}</h1>

                    <div class="product-info__meta">
                        <span class="product-info__sku">Арт: {{ product.sku }}</span>
                        <span v-if="product.brand" class="product-info__meta-sep">·</span>
                        <Link v-if="product.brand" :href="`/brands/${product.brand.slug}`" class="product-info__brand">
                            {{ product.brand.name }}
                        </Link>
                    </div>

                    <div class="product-info__price-row">
                        <span class="product-info__price">{{ fmt(product.price) }}</span>
                        <span v-if="product.oldPrice" class="product-info__old-price">{{ fmt(product.oldPrice) }}</span>
                    </div>

                    <div class="product-info__divider"></div>

                    <div class="product-info__actions">
                        <div class="product-info__qty">
                            <button class="product-info__qty-btn" @click="qty > 1 && qty--">−</button>
                            <input class="product-info__qty-input" type="number" v-model.number="qty" min="1" />
                            <button class="product-info__qty-btn" @click="qty++">+</button>
                        </div>
                        <button class="product-info__cart-btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            До кошика
                        </button>
                    </div>

                    <div class="product-info__secondary">
                        <button class="product-info__icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                            </svg>
                            Порівняти
                        </button>
                        <button class="product-info__icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                            У вішліст
                        </button>
                    </div>

                </div>
            </div>

            <!-- Tabs: description / attributes -->
            <div class="product-tabs">
                <div class="product-tabs__nav">
                    <button
                        class="product-tabs__btn"
                        :class="{ 'product-tabs__btn--active': activeTab === 'description' }"
                        @click="activeTab = 'description'"
                    >Опис</button>
                    <button
                        class="product-tabs__btn"
                        :class="{ 'product-tabs__btn--active': activeTab === 'attributes' }"
                        @click="activeTab = 'attributes'"
                    >Характеристики</button>
                </div>
                <div class="product-tabs__content">
                    <div v-if="activeTab === 'description'" class="product-tabs__text">
                        <p v-if="product.description">{{ product.description }}</p>
                        <p v-else class="product-tabs__empty">Опис не заповнено.</p>
                    </div>
                    <div v-else class="product-tabs__attrs">
                        <table v-if="product.attributes.length" class="product-attrs">
                            <tbody>
                                <tr v-for="attr in product.attributes" :key="attr.name">
                                    <td class="product-attrs__name">{{ attr.name }}</td>
                                    <td class="product-attrs__value">{{ attr.value }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-else class="product-tabs__empty">Характеристики не заповнено.</p>
                    </div>
                </div>
            </div>

            <!-- Related products -->
            <div v-if="related.length" class="product-related">
                <h2 class="product-related__title">Схожі товари</h2>
                <div class="cat-grid">
                    <ProductCard v-for="p in related" :key="p.id" :product="p" />
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineProps({
    product: Object,
    related: Array,
});

const activeTab = ref('description');
const qty = ref(1);

function fmt(price) {
    return price.toLocaleString('uk-UA') + ' ₴';
}
</script>
