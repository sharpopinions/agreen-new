<template>
    <article class="product-card" :class="{ 'product-card--list': mode === 'list' }">
        <div class="product-card__top">
            <span v-if="product.badge" class="product-card__badge">{{ product.badge.name }}</span>
            <div class="product-card__tools">
                <button class="product-card__tool" type="button" aria-label="Додати до порівняння">
                    <Icon name="compare" :size="20" />
                </button>
                <button class="product-card__tool" type="button" aria-label="Додати в обране">
                    <Icon name="heart" :size="22" />
                </button>
            </div>
        </div>

        <Link :href="`/p/${product.slug}`" class="product-card__img img-ph" :aria-label="product.name" />

        <div class="product-card__body">
            <Link :href="`/p/${product.slug}`" class="product-card__name">{{ product.name }}</Link>
            <div class="product-card__sku">Артикул: {{ product.sku }}</div>

            <div class="product-card__rating">
                <Stars :value="product.rating" />
                <span>{{ product.rating || 0 }}/5 ({{ reviewsLabel }})</span>
            </div>

            <div class="product-card__row">
                <span>Ціна:</span>
                <span class="product-card__prices">
                    <s v-if="product.oldPrice" class="product-card__old-price">{{ fmt(product.oldPrice) }}</s>
                    <span class="product-card__price">{{ fmt(product.price) }}</span>
                </span>
            </div>

            <div class="product-card__row">
                <span class="product-card__stock">
                    <template v-if="product.stock != null">На складі: {{ product.stock }} шт</template>
                </span>
                <Qty v-model="qty" />
            </div>

            <button class="btn btn--primary btn--full product-card__btn" type="button">Замовити</button>
        </div>
    </article>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import Stars from '@/Components/Stars.vue';
import Qty from '@/Components/Qty.vue';
import { formatPrice as fmt, pluralUa } from '@/utils/format';

const props = defineProps({
    product: Object,
    mode:    { type: String, default: 'grid' },
});

const qty = ref(1);

const reviewsLabel = computed(() => pluralUa(props.product.reviews ?? 0, 'відгук', 'відгуки', 'відгуків'));
</script>
