<template>
    <div class="cart-row">
        <div class="cart-row__product">
            <Link :href="`/p/${item.slug}`" class="cart-row__img">
                <img v-if="item.image" :src="item.image" :alt="item.name" />
                <ImgPlaceholder v-else :h="80" label="" :seed="item.sku" />
            </Link>
            <div>
                <div class="cart-row__sku">Арт: {{ item.sku }}</div>
                <Link :href="`/p/${item.slug}`" class="cart-row__name">{{ item.name }}</Link>
                <div class="cart-row__unit">
                    <template v-if="item.preorder"><span class="cart-tag">Під замовлення</span></template>
                    <template v-else>{{ fmt(item.price) }} / шт</template>
                </div>
            </div>
        </div>
        <div class="cart-row__c">
            <CartQty :value="item.quantity" :max="item.stock" :disabled="pending" @change="q => setQty(item.id, q)" />
        </div>
        <div class="cart-row__r cart-row__sum">{{ fmt(item.total) }}</div>
        <div class="cart-row__r">
            <button class="cart-row__remove" :disabled="pending" aria-label="Видалити з кошика" @click="remove(item.id)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import CartQty from '@/Components/Cart/CartQty.vue';
import { useCart } from '@/composables/useCart';
import { formatPrice as fmt } from '@/utils/format';

defineProps({ item: { type: Object, required: true } });

const { pending, setQty, remove } = useCart();
</script>
