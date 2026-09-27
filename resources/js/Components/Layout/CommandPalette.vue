<template>
    <Teleport to="body">
        <div v-if="open" class="cmdk" @mousedown.self="closePalette">
            <div class="cmdk__panel" role="dialog" aria-modal="true" aria-label="Пошук по сайту">

                <div class="cmdk__search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input
                        ref="input"
                        v-model="query"
                        class="cmdk__input"
                        placeholder="Пошук сторінок, товарів, брендів..."
                        role="combobox"
                        aria-controls="cmdk-results"
                        :aria-activedescendant="filtered[selected] ? `cmdk-item-${selected}` : undefined"
                        @keydown="onKeydown"
                    />
                    <kbd class="cmdk__kbd">ESC</kbd>
                </div>

                <div id="cmdk-results" ref="list" class="cmdk__results" role="listbox">
                    <div v-if="!filtered.length" class="cmdk__empty">
                        <div>{{ loading ? 'Шукаємо…' : 'Нічого не знайдено' }}</div>
                        <div v-if="!loading" class="cmdk__empty-hint">Спробуйте інші ключові слова</div>
                    </div>
                    <div v-for="group in groups" :key="group.kind">
                        <div class="cmdk__group">{{ group.kind }}</div>
                        <div
                            v-for="item in group.items"
                            :id="`cmdk-item-${item.idx}`"
                            :key="item.idx"
                            class="cmdk__item"
                            :class="{ 'cmdk__item--active': item.idx === selected }"
                            role="option"
                            :aria-selected="item.idx === selected"
                            @mousemove="selected = item.idx"
                            @click="select(item)"
                        >
                            <span class="cmdk__icon">{{ item.icon }}</span>
                            <span class="cmdk__text">
                                <span class="cmdk__name">{{ item.name }}</span>
                                <span v-if="item.sub" class="cmdk__sub">{{ item.sub }}</span>
                            </span>
                            <span v-if="item.idx === selected" class="cmdk__enter">↵</span>
                        </div>
                    </div>
                </div>

                <div class="cmdk__footer">
                    <div class="cmdk__hints">
                        <span><kbd>↑</kbd> <kbd>↓</kbd> навігація</span>
                        <span><kbd>↵</kbd> вибрати</span>
                        <span><kbd>ESC</kbd> закрити</span>
                    </div>
                    <span>{{ pluralUa(filtered.length, 'результат', 'результати', 'результатів') }}</span>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useCommandPalette } from '@/composables/useCommandPalette';
import { useTheme } from '@/composables/useTheme';
import { pluralUa } from '@/utils/format';

const page = usePage();
const { open, openPalette, closePalette } = useCommandPalette();
const { toggleTheme } = useTheme();

const query    = ref('');
const selected = ref(0);
const products = ref([]);
const loading  = ref(false);
const input    = ref(null);
const list     = ref(null);

// Сторінки — лише ті, що вже існують на сайті
const pages = [
    { name: 'Головна',            icon: '🏠', href: '/' },
    { name: 'Каталог',            icon: '⊞',  href: '/catalog' },
    { name: 'Усі товари',         icon: '📦', href: '/catalog/all' },
    { name: 'Акційні товари',     icon: '🔥', href: '/catalog?sale=1' },
    { name: 'Про нас',            icon: 'ℹ️', href: '/about' },
];

const fmt = (p) => Number(p).toLocaleString('uk-UA') + ' ₴';

const staticItems = computed(() => [
    ...pages.map(p => ({ kind: 'Сторінка', ...p })),
    ...(page.props.nav?.categories ?? []).map(c => ({ kind: 'Категорія', name: c.name, icon: '📦', href: `/catalog/${c.slug}` })),
    ...(page.props.nav?.brands ?? []).map(b => ({ kind: 'Бренд', name: b.name, icon: '🏭', href: `/catalog?brand[]=${b.id}` })),
    { kind: 'Дія', name: 'Перемкнути тему', icon: '🌗', action: toggleTheme },
]);

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return staticItems.value.slice(0, 12);

    const local = staticItems.value.filter(i =>
        i.name.toLowerCase().includes(q) || i.kind.toLowerCase().includes(q));
    const found = products.value.map(p => ({
        kind: 'Товар', name: p.name, sub: `${p.sku} · ${fmt(p.price)}`, icon: '🔧', href: `/p/${p.slug}`,
    }));
    // «Дія» завжди в кінці
    return [...local.filter(i => i.kind !== 'Дія'), ...found, ...local.filter(i => i.kind === 'Дія')];
});

const groups = computed(() => {
    const map = new Map();
    filtered.value.forEach((item, idx) => {
        if (!map.has(item.kind)) map.set(item.kind, []);
        map.get(item.kind).push({ ...item, idx });
    });
    return [...map].map(([kind, items]) => ({ kind, items }));
});

// Пошук товарів на сервері з невеликою затримкою
let timer = null;
let requestId = 0;
watch(query, (q) => {
    selected.value = 0;
    clearTimeout(timer);
    const term = q.trim();
    if (term.length < 2) { products.value = []; loading.value = false; return; }
    loading.value = true;
    timer = setTimeout(async () => {
        const id = ++requestId;
        try {
            const res = await fetch(`/search?q=${encodeURIComponent(term)}`, { headers: { Accept: 'application/json' } });
            const data = res.ok ? await res.json() : { products: [] };
            if (id === requestId) products.value = data.products ?? [];
        } catch {
            if (id === requestId) products.value = [];
        } finally {
            if (id === requestId) loading.value = false;
        }
    }, 200);
});

watch(open, async (isOpen) => {
    if (!isOpen) return;
    query.value = '';
    selected.value = 0;
    products.value = [];
    await nextTick();
    input.value?.focus();
});

function select(item) {
    closePalette();
    if (item.action) item.action();
    else if (item.href) router.visit(item.href);
}

function scrollToSelected() {
    nextTick(() => list.value?.querySelector('.cmdk__item--active')?.scrollIntoView({ block: 'nearest' }));
}

function onKeydown(e) {
    if (e.key === 'Escape') { closePalette(); return; }
    if (e.key === 'ArrowDown') { e.preventDefault(); selected.value = Math.min(filtered.value.length - 1, selected.value + 1); scrollToSelected(); }
    if (e.key === 'ArrowUp')   { e.preventDefault(); selected.value = Math.max(0, selected.value - 1); scrollToSelected(); }
    if (e.key === 'Enter' && filtered.value[selected.value]) { e.preventDefault(); select(filtered.value[selected.value]); }
}

// Глобальна гаряча клавіша Cmd+K / Ctrl+K
function onGlobalKeydown(e) {
    // e.code, а не e.key — щоб працювало й в українській розкладці («л»)
    if ((e.metaKey || e.ctrlKey) && e.code === 'KeyK') {
        e.preventDefault();
        open.value ? closePalette() : openPalette();
    }
}

onMounted(() => window.addEventListener('keydown', onGlobalKeydown));
onBeforeUnmount(() => { window.removeEventListener('keydown', onGlobalKeydown); clearTimeout(timer); });
</script>
