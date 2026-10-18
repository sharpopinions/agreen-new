<template>
    <!-- Claude Design → FavoritePopup: список бажань при наведенні на сердечко в хедері -->
    <div class="wish-menu" @mouseenter="show" @mouseleave="hide" @focusin="show" @focusout="onFocusOut">
        <Link href="/wishlist" class="header__icon-btn header__icon-btn--badge" :class="{ 'header__icon-btn--open': open }" aria-label="Обрані товари">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            <span v-if="wishlist.length" class="header__badge">{{ wishlist.length }}</span>
        </Link>

        <div v-if="open" class="wish-menu__panel">
            <div v-if="!wishlist.length" class="wish-menu__empty">
                <div class="wish-menu__empty-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" /></svg>
                </div>
                <div class="wish-menu__empty-title">Список бажань порожній</div>
                <div class="wish-menu__empty-text">Додайте товари, щоб не загубити їх</div>
                <Link href="/catalog" class="btn btn--primary btn--sm" @click="open = false">До каталогу</Link>
            </div>

            <template v-else>
                <div class="wish-menu__head">
                    <span class="wish-menu__title">Список бажань · {{ wishlist.length }}</span>
                    <Link href="/wishlist" class="wish-menu__all" @click="open = false">Усі товари →</Link>
                </div>
                <div class="wish-menu__items">
                    <div v-for="p in items" :key="p.id" class="wish-menu__item">
                        <Link :href="`/p/${p.slug}`" class="wish-menu__thumb" @click="open = false">
                            <img v-if="p.image" :src="p.image" :alt="p.name" />
                            <ImgPlaceholder v-else :h="48" label="" />
                        </Link>
                        <Link :href="`/p/${p.slug}`" class="wish-menu__info" @click="open = false">
                            <span class="wish-menu__name">{{ p.name }}</span>
                            <span class="wish-menu__price">{{ fmt(p.price) }}</span>
                        </Link>
                        <button type="button" class="wish-menu__remove" :aria-label="`Прибрати ${p.name}`" @click="toggleWishlist(p.id)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                        </button>
                    </div>
                    <div v-if="wishlist.length > 4" class="wish-menu__more">та ще {{ wishlist.length - 4 }}…</div>
                </div>
                <div class="wish-menu__foot">
                    <Link href="/wishlist" class="btn btn--primary btn--sm btn--full" @click="open = false">Переглянути всі ({{ wishlist.length }})</Link>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import { useLists } from '@/composables/useLists';
import { formatPrice as fmt } from '@/utils/format';

const { wishlist, toggleWishlist } = useLists();
const open = ref(false);
const items = ref([]);
let loadedFor = null;
let timer;

async function load() {
    const key = wishlist.value.slice(0, 4).join(',');
    if (!wishlist.value.length || key === loadedFor) return;
    try {
        const res = await fetch('/wishlist/preview', { headers: { Accept: 'application/json' } });
        if (res.ok) {
            items.value = (await res.json()).items;
            loadedFor = key;
        }
    } catch {
        // мережа недоступна — попап покаже лише лічильник
    }
}

function show() {
    clearTimeout(timer);
    open.value = true;
    load();
}

function hide() {
    clearTimeout(timer);
    timer = setTimeout(() => (open.value = false), 150);
}

function onFocusOut(e) {
    if (!e.currentTarget.contains(e.relatedTarget)) hide();
}

// Список змінився (додали / прибрали товар) — оновити попап при наступному відкритті
watch(wishlist, () => {
    items.value = items.value.filter(p => wishlist.value.includes(p.id));
    loadedFor = null;
    if (open.value) load();
});
</script>
