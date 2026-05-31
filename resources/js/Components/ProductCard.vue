<template>
    <div class="product-card">
        <div class="product-card__img">
            <span v-if="product.badge" class="product-card__badge" :class="badgeClass">
                {{ product.badge }}
            </span>
            <button class="product-card__wish" aria-label="Вішліст">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            <span class="product-card__img-label">Фото товару</span>
        </div>
        <div class="product-card__body">
            <div class="product-card__sku">Арт: {{ product.sku }}</div>
            <div class="product-card__name">{{ product.name }}</div>
            <div class="product-card__rating">
                <span class="product-card__stars">
                    <svg v-for="i in 5" :key="i" width="12" height="12" viewBox="0 0 24 24"
                        :fill="i <= Math.round(product.rating) ? '#f59e0b' : 'none'"
                        :stroke="i <= Math.round(product.rating) ? '#f59e0b' : '#d4d4d8'"
                        stroke-width="1.5">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                    </svg>
                </span>
                <span class="product-card__reviews">{{ product.rating }} ({{ product.reviews }})</span>
            </div>
            <div class="product-card__price-row">
                <span class="product-card__price">{{ fmt(product.price) }}</span>
                <span v-if="product.oldPrice" class="product-card__old-price">{{ fmt(product.oldPrice) }}</span>
            </div>
            <div class="product-card__stock">В наявності: {{ product.inStock }} шт</div>
            <button class="product-card__btn">До кошика</button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    product: Object,
});

const badgeClass = computed(() => ({
    'product-card__badge--sale': props.product.badge === 'Акція',
    'product-card__badge--new':  props.product.badge === 'Новинка',
    'product-card__badge--hit':  props.product.badge === 'Хіт',
}));

function fmt(price) {
    return price.toLocaleString('uk-UA') + ' ₴';
}
</script>
