<template>
    <!-- Режим список -->
    <div v-if="mode === 'list'" class="product-card product-card--list">
        <Link :href="`/p/${product.slug}`" class="product-card__img-sm">
            <img v-if="product.image" :src="product.image" :alt="product.name" class="product-card__photo" loading="lazy" />
        </Link>
        <div class="product-card__body">
            <div class="product-card__sku">Арт: {{ product.sku }}</div>
            <Link :href="`/p/${product.slug}`" class="product-card__name">{{ product.name }}</Link>
            <div class="product-card__rating">
                <span class="product-card__stars">
                    <svg v-for="i in 5" :key="i" width="12" height="12" viewBox="0 0 24 24"
                        :fill="i <= Math.round(product.rating) ? '#f59e0b' : 'none'"
                        :stroke="i <= Math.round(product.rating) ? '#f59e0b' : '#d4d4d8'" stroke-width="1.5">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                    </svg>
                </span>
            </div>
        </div>
        <div class="product-card__right">
            <span class="product-card__price">{{ fmt(product.price) }}</span>
            <div class="product-card__list-actions">
                <button
                    class="product-card__icon"
                    :class="{ 'product-card__icon--wish': inWishlist(product.id) }"
                    :aria-label="inWishlist(product.id) ? 'Прибрати з обраного' : 'Додати в обране'"
                    :title="inWishlist(product.id) ? 'Прибрати з обраного' : 'Додати в обране'"
                    :aria-pressed="inWishlist(product.id)"
                    @click="toggleWishlist(product.id)"
                ><svg width="16" height="16" viewBox="0 0 24 24" :fill="inWishlist(product.id) ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>
                <button
                    class="product-card__icon"
                    :class="{ 'product-card__icon--compare': inCompare(product.id) }"
                    :aria-label="inCompare(product.id) ? 'Прибрати з порівняння' : 'Додати до порівняння'"
                    :title="inCompare(product.id) ? 'Прибрати з порівняння' : 'Додати до порівняння'"
                    :aria-pressed="inCompare(product.id)"
                    @click="toggleCompare(product.id)"
                ><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg></button>
                <button class="product-card__btn" :disabled="pending" @click="add(product.id)">До кошика</button>
            </div>
        </div>
    </div>

    <!-- Режим плитка -->
    <div v-else class="product-card">
        <Link :href="`/p/${product.slug}`" class="product-card__img">
            <span v-if="product.badge" class="product-card__badge"
                :style="{ color: product.badge.color, backgroundColor: product.badge.bgColor }">
                {{ product.badge.name }}
            </span>
            <button
                class="product-card__wish"
                :class="{ 'product-card__wish--active': inWishlist(product.id) }"
                :aria-label="inWishlist(product.id) ? 'Прибрати з обраного' : 'Додати в обране'"
                :aria-pressed="inWishlist(product.id)"
                @click.prevent.stop="toggleWishlist(product.id)"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" :fill="inWishlist(product.id) ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            <button
                class="product-card__wish product-card__compare"
                :class="{ 'product-card__compare--active': inCompare(product.id) }"
                :aria-label="inCompare(product.id) ? 'Прибрати з порівняння' : 'Додати до порівняння'"
                :title="inCompare(product.id) ? 'Прибрати з порівняння' : 'Додати до порівняння'"
                :aria-pressed="inCompare(product.id)"
                @click.prevent.stop="toggleCompare(product.id)"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg>
            </button>
            <img v-if="product.image" :src="product.image" :alt="product.name" class="product-card__photo" loading="lazy" />
            <span v-else class="product-card__img-label">ФОТО ТОВАРУ</span>
        </Link>
        <div class="product-card__body">
            <div class="product-card__sku">Арт: {{ product.sku }}</div>
            <Link :href="`/p/${product.slug}`" class="product-card__name">{{ product.name }}</Link>
            <div class="product-card__rating">
                <span class="product-card__stars">
                    <svg v-for="i in 5" :key="i" width="12" height="12" viewBox="0 0 24 24"
                        :fill="i <= Math.round(product.rating) ? '#f59e0b' : 'none'"
                        :stroke="i <= Math.round(product.rating) ? '#f59e0b' : '#d4d4d8'" stroke-width="1.5">
                        <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                    </svg>
                </span>
                <span class="product-card__reviews">{{ product.rating }} ({{ product.reviews }})</span>
            </div>
            <div class="product-card__price-row">
                <span class="product-card__price">{{ fmt(product.price) }}</span>
                <span v-if="product.oldPrice" class="product-card__old-price">{{ fmt(product.oldPrice) }}</span>
            </div>
            <div v-if="product.stock === 0" class="product-card__stock product-card__stock--order">Під замовлення</div>
            <div v-else-if="product.stock != null" class="product-card__stock">
                В наявності: {{ product.stock }} шт
            </div>
            <button class="product-card__btn" :disabled="pending" @click="add(product.id)">До кошика</button>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';
import { useLists } from '@/composables/useLists';

const { add, pending } = useCart();
const { inWishlist, inCompare, toggleWishlist, toggleCompare } = useLists();

defineProps({
    product: Object,
    mode:    { type: String, default: 'grid' },
});

function fmt(price) {
    return price.toLocaleString('uk-UA') + ' ₴';
}
</script>
