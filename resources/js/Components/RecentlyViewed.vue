<template>
    <section v-if="items.length" class="recently">
        <h2 class="recently__title">Нещодавно переглянуті</h2>
        <div class="recently__track">
            <ProductCard v-for="p in items" :key="p.id" :product="p" class="recently__item" />
        </div>
    </section>
</template>

<script setup>
// ТЗ: «Нещодавно переглянуті товари» внизу сторінки — картки зі скролом вбік.
// Зберігаємо короткі знімки товарів у localStorage цього браузера.
import { ref, onMounted } from 'vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    current: { type: Object, default: null }, // товар, який зараз відкрито
    limit:   { type: Number, default: 8 },
});

const KEY = 'ag_recently_viewed';
const items = ref([]);

function read() {
    try { return JSON.parse(localStorage.getItem(KEY)) ?? []; } catch { return []; }
}

function write(list) {
    try { localStorage.setItem(KEY, JSON.stringify(list)); } catch { /* приватний режим тощо */ }
}

onMounted(() => {
    const list = read();
    items.value = list.filter(p => p.id !== props.current?.id).slice(0, props.limit);

    if (props.current) {
        const c = props.current;
        const snapshot = {
            id: c.id, name: c.name, slug: c.slug, sku: c.sku,
            price: c.price, oldPrice: c.oldPrice, rating: c.rating, reviews: c.reviews,
            stock: c.stock, badge: c.badges?.[0] ?? c.badge ?? null,
        };
        write([snapshot, ...list.filter(p => p.id !== c.id)].slice(0, 12));
    }
});
</script>
