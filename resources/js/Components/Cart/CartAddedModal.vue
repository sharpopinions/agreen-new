<template>
    <Teleport to="body">
        <div v-if="open && item" class="cart-modal" @mousedown.self="close">
            <div class="cart-modal__panel" role="dialog" aria-modal="true" aria-labelledby="cart-modal-title">
                <div class="cart-modal__head">
                    <div class="cart-modal__title" id="cart-modal-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Додано до кошика
                    </div>
                    <button class="cart-modal__close" aria-label="Закрити" @click="close">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Доданий товар: фото, назва, артикул, кількість, вартість, видалення (ТЗ) -->
                <div class="cart-line">
                    <div class="cart-line__img">
                        <img v-if="item.image" :src="item.image" :alt="item.name" />
                        <ImgPlaceholder v-else :h="72" label="" :seed="item.sku" />
                    </div>
                    <div class="cart-line__info">
                        <div class="cart-line__sku">Арт: {{ item.sku }}</div>
                        <Link :href="`/p/${item.slug}`" class="cart-line__name" @click="close">{{ item.name }}</Link>
                        <div v-if="item.preorder" class="cart-line__preorder">Під замовлення — окреме замовлення без оплати</div>
                        <div v-else class="cart-line__unit">{{ fmt(item.price) }} / шт</div>
                    </div>
                    <CartQty :value="item.quantity" :max="item.stock" :disabled="pending" small @change="q => setQty(item.id, q)" />
                    <div class="cart-line__total">{{ fmt(item.total) }}</div>
                    <button class="cart-line__remove" aria-label="Видалити з кошика" @click="removeAndClose">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="cart-modal__sum">
                    <span>У кошику {{ pluralUa(cart.count, 'товар', 'товари', 'товарів') }} на суму</span>
                    <strong>{{ fmt(cart.total) }}</strong>
                </div>

                <div class="cart-modal__actions">
                    <Link href="/cart" class="btn btn--primary" @click="close">Перейти до кошика</Link>
                    <Link href="/checkout" class="btn btn--outline" @click="close">Оформити замовлення</Link>
                    <button class="cart-modal__continue" @click="close">Продовжити покупки →</button>
                </div>

                <div v-if="recommendations.length" class="cart-modal__recs">
                    <div class="cart-modal__label">Супутні товари</div>
                    <div class="cart-modal__recs-grid">
                        <Link v-for="p in recommendations" :key="p.id" :href="`/p/${p.slug}`" class="cart-rec" @click="close">
                            <ImgPlaceholder :h="60" label="" :seed="p.sku" />
                            <span class="cart-rec__name">{{ p.name }}</span>
                            <span class="cart-rec__price">{{ fmt(p.price) }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import CartQty from '@/Components/Cart/CartQty.vue';
import { useCart } from '@/composables/useCart';
import { formatPrice as fmt, pluralUa } from '@/utils/format';

const page = usePage();
const { cart, pending, setQty, remove } = useCart();

const open = ref(false);
const productId = ref(null);
const recommendations = ref([]);

const item = computed(() => cart.value.items.find(i => i.id === productId.value));

// Попап відкривається, коли сервер повідомляє про додавання товару
watch(() => page.props.flash?.cartAdded, (added) => {
    if (!added) return;
    productId.value = added.productId;
    recommendations.value = added.recommendations ?? [];
    open.value = true;
}, { immediate: true });

// Якщо товар видалили з кошика — закриваємо
watch(item, (i) => { if (open.value && !i) open.value = false; });

function close() { open.value = false; }

function removeAndClose() {
    remove(productId.value);
    close();
}

function onKey(e) { if (e.key === 'Escape') close(); }
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));
</script>
