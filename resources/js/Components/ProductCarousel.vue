<template>
    <div class="carousel">
        <button class="carousel__arrow carousel__arrow--prev" type="button" aria-label="Назад" @click="scroll(-1)">
            <Icon name="chevron-left" :size="40" :stroke-width="1" />
        </button>
        <div ref="track" class="carousel__track">
            <ProductCard v-for="product in products" :key="product.id" :product="product" class="carousel__item" />
        </div>
        <button class="carousel__arrow carousel__arrow--next" type="button" aria-label="Вперед" @click="scroll(1)">
            <Icon name="chevron-right" :size="40" :stroke-width="1" />
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Icon from '@/Components/Icon.vue';
import ProductCard from '@/Components/ProductCard.vue';

defineProps({
    products: { type: Array, default: () => [] },
});

const track = ref(null);

function scroll(dir) {
    const el = track.value;
    if (!el) return;
    el.scrollBy({ left: dir * el.clientWidth, behavior: 'smooth' });
}
</script>
