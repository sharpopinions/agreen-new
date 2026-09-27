<template>
    <Teleport to="body">
        <div v-if="drawerOpen" class="cart-drawer">
            <div class="cart-drawer__backdrop" @click="closeDrawer"></div>
            <aside class="cart-drawer__panel" role="dialog" aria-modal="true" aria-label="Кошик">
                <div class="cart-drawer__head">
                    <div class="cart-drawer__title">Кошик <template v-if="cart.count">({{ cart.count }})</template></div>
                    <button class="cart-modal__close" aria-label="Закрити" @click="closeDrawer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div v-if="!cart.items.length" class="cart-drawer__empty">
                    <div class="cart-empty__icon cart-empty__icon--sm">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div class="cart-drawer__empty-title">Кошик порожній</div>
                    <div class="cart-drawer__empty-text">Додайте товари, щоб оформити замовлення</div>
                    <Link href="/catalog" class="btn btn--primary" @click="closeDrawer">До каталогу</Link>
                </div>

                <template v-else>
                    <div class="cart-drawer__items">
                        <div v-for="i in cart.items" :key="i.id" class="cart-drawer__item">
                            <div class="cart-drawer__img">
                                <img v-if="i.image" :src="i.image" :alt="i.name" />
                                <ImgPlaceholder v-else :h="56" label="" :seed="i.sku" />
                            </div>
                            <div class="cart-drawer__info">
                                <Link :href="`/p/${i.slug}`" class="cart-drawer__name" @click="closeDrawer">{{ i.name }}</Link>
                                <div class="cart-drawer__meta">
                                    {{ i.quantity }} шт × {{ fmt(i.price) }}
                                    <span v-if="i.preorder" class="cart-tag">Під замовлення</span>
                                </div>
                            </div>
                            <div class="cart-drawer__sum">{{ fmt(i.total) }}</div>
                        </div>
                    </div>
                    <div class="cart-drawer__foot">
                        <div class="cart-drawer__total"><span>Разом:</span><span>{{ fmt(cart.total) }}</span></div>
                        <Link href="/checkout" class="btn btn--primary btn--full" @click="closeDrawer">Оформити замовлення</Link>
                        <Link href="/cart" class="btn btn--ghost btn--full cart-drawer__to-cart" @click="closeDrawer">Перейти до кошика</Link>
                    </div>
                </template>
            </aside>
        </div>
    </Teleport>
</template>

<script setup>
import { watch, onMounted, onBeforeUnmount } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import { useCart } from '@/composables/useCart';
import { formatPrice as fmt } from '@/utils/format';

const { cart, drawerOpen, closeDrawer } = useCart();

function onKey(e) { if (e.key === 'Escape') closeDrawer(); }
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

// Не лишати відкритою при переході на іншу сторінку
const off = router.on('navigate', closeDrawer);
onBeforeUnmount(off);

watch(drawerOpen, (v) => { document.body.style.overflow = v ? 'hidden' : ''; });
</script>
