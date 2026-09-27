<template>
    <AppLayout>
        <div class="cart-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Кошик</span>
            </nav>

            <!-- Порожній кошик (Claude Design: CartEmptyPage + ТЗ) -->
            <template v-if="!cart.items.length">
                <div class="cart-empty">
                    <div class="cart-empty__icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <h1 class="cart-empty__title">Ваш кошик порожній</h1>
                    <p class="cart-empty__text">Перейдіть в Каталог та додайте товари, які хочете придбати.</p>
                    <Link href="/catalog" class="btn btn--primary">Перейти в Каталог</Link>
                </div>

                <section v-if="suggestions.length">
                    <h2 class="cart-page__subtitle">Можливо вас зацікавить</h2>
                    <div class="cat-grid">
                        <ProductCard v-for="p in suggestions" :key="p.id" :product="p" />
                    </div>
                </section>
            </template>

            <template v-else>
                <div class="cart-page__head">
                    <h1 class="cart-page__title">Кошик</h1>
                    <button class="cart-page__clear" :disabled="pending" @click="clearCart">Очистити кошик</button>
                </div>

                <div class="cart-page__layout">
                    <div class="cart-page__list">
                        <div class="cart-row cart-row--head">
                            <span>Товар</span><span class="cart-row__c">Кількість</span><span class="cart-row__r">Сума</span><span></span>
                        </div>

                        <CartRow v-for="i in regular" :key="i.id" :item="i" />

                        <template v-if="preorder.length">
                            <div class="cart-group">
                                <div class="cart-group__title">Передзамовлення</div>
                                <div class="cart-group__note">
                                    Ці товари під замовлення: оформлюються окремим замовленням без оплати та вибору доставки.
                                    Ціну та строк поставки підтвердить менеджер.
                                </div>
                            </div>
                            <CartRow v-for="i in preorder" :key="i.id" :item="i" />
                        </template>
                    </div>

                    <aside class="cart-summary">
                        <div class="cart-summary__title">Ваше замовлення</div>
                        <div class="cart-summary__row">
                            <span>Товари ({{ cart.count }}):</span><span>{{ fmt(cart.total) }}</span>
                        </div>
                        <div class="cart-summary__row cart-summary__row--sep">
                            <span>Доставка:</span><span class="cart-summary__muted">розраховується</span>
                        </div>
                        <div class="cart-summary__total">
                            <span>Разом:</span><span>{{ fmt(cart.total) }}</span>
                        </div>
                        <p v-if="preorder.length" class="cart-summary__hint">
                            Буде створено {{ regular.length ? 'два замовлення: звичайне та передзамовлення' : 'передзамовлення' }}.
                        </p>
                        <Link href="/checkout" class="btn btn--primary btn--full">Оформити замовлення</Link>
                        <Link href="/catalog" class="btn btn--ghost btn--full cart-summary__continue">Продовжити покупки</Link>
                    </aside>
                </div>
            </template>

            <RecentlyViewed />
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import RecentlyViewed from '@/Components/RecentlyViewed.vue';
import CartRow from '@/Components/Cart/CartRow.vue';
import { useCart } from '@/composables/useCart';
import { formatPrice as fmt } from '@/utils/format';

defineProps({
    suggestions: { type: Array, default: () => [] },
});

const { cart, pending, clear } = useCart();

const regular  = computed(() => cart.value.items.filter(i => !i.preorder));
const preorder = computed(() => cart.value.items.filter(i => i.preorder));

function clearCart() {
    if (window.confirm('Видалити всі товари з кошика?')) clear();
}
</script>
