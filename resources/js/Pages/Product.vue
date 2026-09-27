<template>
    <AppLayout>
        <div class="product-page">

            <!-- Хлібні крихти: Головна › Каталог › Категорія › Підкатегорія › Товар -->
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/catalog" class="breadcrumbs__item">Каталог</Link>
                <template v-for="c in product.breadcrumbs" :key="c.slug">
                    <span class="breadcrumbs__sep">›</span>
                    <Link :href="`/catalog/${c.slug}`" class="breadcrumbs__item">{{ c.name }}</Link>
                </template>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ product.name }}</span>
            </nav>

            <div class="product-page__body">

                <!-- Галерея -->
                <div class="product-gallery">
                    <div class="product-gallery__main">
                        <img v-if="images.length" :src="images[activeImg].url" :alt="images[activeImg].alt || product.name" class="product-gallery__img" />
                        <ImgPlaceholder v-else :h="340" :label="`фото товару ${activeImg + 1}`" :seed="`${product.sku}-${activeImg}`" />
                    </div>
                    <div v-if="thumbs.length" class="product-gallery__thumbs">
                        <button
                            v-for="(thumb, i) in thumbs"
                            :key="i"
                            class="product-gallery__thumb"
                            :class="{ 'product-gallery__thumb--active': activeImg === i }"
                            :aria-label="`Фото ${i + 1}`"
                            @click="activeImg = i"
                        >
                            <img v-if="thumb" :src="thumb.url" :alt="thumb.alt" />
                            <ImgPlaceholder v-else :h="70" :label="String(i + 1)" :seed="`${product.sku}-${i}`" />
                        </button>
                    </div>
                </div>

                <!-- Інформація -->
                <div class="product-info">
                    <div v-if="product.badges.length || isModified" class="product-info__badges">
                        <span v-for="b in product.badges" :key="b.name" class="badge" :class="badgeClass(b.name)">{{ b.name }}</span>
                    </div>

                    <h1 class="product-info__name">{{ product.name }}</h1>

                    <div class="product-info__rating">
                        <span class="stars">
                            <svg v-for="i in 5" :key="i" width="12" height="12" viewBox="0 0 24 24" :class="{ 'stars__on': i <= Math.round(product.rating) }">
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                            </svg>
                        </span>
                        <span>{{ product.rating }}/5 ({{ pluralUa(product.reviews ?? 0, 'відгук', 'відгуки', 'відгуків') }})</span>
                        <span class="product-info__dot">·</span>
                        <button class="product-info__review-link" @click="openTab('reviews')">Залишити відгук</button>
                    </div>

                    <div class="product-info__meta">
                        <span>Артикул: {{ product.sku }}</span>
                        <template v-if="product.brand">
                            <span>·</span>
                            <span>Бренд: <Link :href="`/catalog?brand[]=${product.brand.id}`" class="product-info__brand">{{ product.brand.name }}</Link></span>
                        </template>
                        <template v-if="isModified">
                            <span>·</span>
                            <span class="pill pill--warn">⚙ Модифікований</span>
                        </template>
                    </div>

                    <!-- Знято з виробництва / рекомендуємо новішу модель (ТЗ) -->
                    <div v-if="product.availability === 'discontinued'" class="notice notice--warn">
                        <span>⚠</span>
                        <span>
                            Увага: цей товар знято з виробництва. Ми пропонуємо сучасну заміну —
                            <Link :href="`/p/${product.replacement.slug}`" class="notice__link">{{ product.replacement.name }}</Link>
                            (арт. {{ product.replacement.sku }}).
                        </span>
                    </div>
                    <div v-else-if="product.replacement" class="notice">
                        <span>ℹ</span>
                        <span>
                            Рекомендуємо новішу модель:
                            <Link :href="`/p/${product.replacement.slug}`" class="notice__link">{{ product.replacement.name }}</Link>
                            (арт. {{ product.replacement.sku }}).
                        </span>
                    </div>

                    <!-- Попередження з адмінки, напр. «працює лише з Активатором X» -->
                    <div v-if="product.warning" class="notice notice--warn">
                        <span>⚠</span><span>{{ product.warning }}</span>
                    </div>

                    <!-- Ціна / статус -->
                    <div class="product-info__price-block">
                        <template v-if="product.availability === 'on_order'">
                            <div class="product-info__status product-info__status--order">
                                <span>⌛</span> Під замовлення<template v-if="product.preorderDays"> · орієнтовно {{ pluralUa(product.preorderDays, 'день', 'дні', 'днів') }}</template>
                            </div>
                            <p class="product-info__hint">Ціну та строк поставки уточнить менеджер після оформлення запиту.</p>
                        </template>

                        <template v-else-if="product.availability === 'discontinued'">
                            <div class="product-info__status product-info__status--muted">Знято з виробництва</div>
                        </template>

                        <template v-else-if="product.partnerPrice">
                            <div class="product-info__price-row">
                                <span class="product-info__price">{{ fmt(product.partnerPrice) }}</span>
                                <span class="pill pill--info">Партнерська ціна</span>
                            </div>
                            <div class="product-info__retail">Роздрібна: {{ fmt(product.price) }}</div>
                            <div class="product-info__status product-info__status--ok">✓ В наявності: {{ product.stock }} шт</div>
                        </template>

                        <template v-else>
                            <div class="product-info__price-row">
                                <span class="product-info__price">{{ fmt(product.price) }}</span>
                                <span v-if="product.oldPrice" class="product-info__old-price">{{ fmt(product.oldPrice) }}</span>
                            </div>
                            <div class="product-info__status product-info__status--ok">✓ В наявності: {{ product.stock }} шт</div>
                        </template>
                    </div>

                    <!-- Дії -->
                    <template v-if="product.availability === 'discontinued'">
                        <Link :href="`/p/${product.replacement.slug}`" class="btn btn--primary btn--lg btn--full">Доступна заміна →</Link>
                    </template>
                    <template v-else>
                        <div class="product-info__buy">
                            <div class="qty">
                                <button class="qty__btn" :disabled="qty <= 1" aria-label="Менше" @click="qty--">−</button>
                                <span class="qty__value">{{ qty }}</span>
                                <button class="qty__btn" :disabled="maxQty !== null && qty >= maxQty" aria-label="Більше" @click="qty++">+</button>
                            </div>
                            <button v-if="product.availability === 'on_order'" class="btn btn--primary btn--lg btn--full" :disabled="pending" @click="add(product.id, qty)">Замовити</button>
                            <button v-else class="btn btn--primary btn--lg btn--full" :disabled="pending" @click="add(product.id, qty)">До кошика</button>
                            <button class="product-info__icon-btn" aria-label="Додати до порівняння" title="Додати до порівняння">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/></svg>
                            </button>
                            <button class="product-info__icon-btn" aria-label="В обране" title="В обране">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            </button>
                        </div>
                        <p v-if="maxQty !== null && qty >= maxQty" class="product-info__hint">На складі лише {{ maxQty }} шт.</p>

                        <button v-if="product.availability === 'on_order'" class="btn btn--outline btn--full">🔔 Повідомити про надходження</button>
                        <button v-else-if="product.partnerPrice" class="btn btn--outline btn--full">📋 Запит на гуртову ціну</button>
                        <button v-else class="btn btn--outline btn--full">Швидке замовлення</button>
                    </template>

                    <!-- Коротко про характеристики -->
                    <div v-if="product.attributes.length" class="product-specs-card">
                        <div class="product-specs-card__title">Характеристики</div>
                        <div v-for="a in product.attributes.slice(0, 5)" :key="a.name" class="product-specs-card__row">
                            <span>{{ a.name }}</span><span>{{ a.value }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таби -->
            <div class="product-tabs" role="tablist">
                <button
                    v-for="t in tabs"
                    :key="t.key"
                    class="product-tabs__btn"
                    :class="{ 'product-tabs__btn--active': tab === t.key }"
                    role="tab"
                    :aria-selected="tab === t.key"
                    @click="tab = t.key"
                >{{ t.label }}</button>
            </div>

            <div ref="tabPanel" class="product-tabs__panel">
                <div v-if="tab === 'desc'" class="product-desc">
                    <div>
                        <h3 class="product-tabs__title">Опис товару</h3>
                        <!-- HTML очищено на сервері (App\Support\Html) -->
                        <div v-if="product.description" class="product-tabs__text product-tabs__text--rich rich-text" v-html="product.description"></div>
                        <p v-else class="product-tabs__empty">Опис не заповнено.</p>
                    </div>
                    <div class="product-desc__img">
                        <img v-if="images[1] || images[0]" :src="(images[1] || images[0]).url" :alt="product.name" />
                        <ImgPlaceholder v-else :h="260" label="фото опис" :seed="`${product.sku}-desc`" />
                    </div>
                </div>

                <div v-else-if="tab === 'specs'" class="product-specs">
                    <h3 class="product-tabs__title">Характеристики</h3>
                    <div class="product-specs__row"><span>Артикул</span><span>{{ product.sku }}</span></div>
                    <div v-if="product.brand" class="product-specs__row"><span>Бренд</span><span>{{ product.brand.name }}</span></div>
                    <div v-for="a in product.attributes" :key="a.name" class="product-specs__row">
                        <span>{{ a.name }}</span><span>{{ a.value }}</span>
                    </div>
                </div>

                <div v-else-if="tab === 'video'" class="product-video">
                    <iframe
                        v-for="id in product.videos"
                        :key="id"
                        :src="`https://www.youtube-nocookie.com/embed/${id}`"
                        title="Відеоогляд"
                        loading="lazy"
                        allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>

                <div v-else-if="tab === 'reviews'" class="product-reviews">
                    <div class="product-reviews__score">
                        <div class="product-reviews__num">{{ product.rating || '—' }}</div>
                        <span class="stars">
                            <svg v-for="i in 5" :key="i" width="12" height="12" viewBox="0 0 24 24" :class="{ 'stars__on': i <= Math.round(product.rating) }">
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                            </svg>
                        </span>
                        <div class="product-reviews__count">{{ pluralUa(product.reviews ?? 0, 'відгук', 'відгуки', 'відгуків') }}</div>
                    </div>
                    <div>
                        <p class="product-tabs__empty">Поділіться досвідом використання — це допоможе іншим покупцям.</p>
                        <button class="btn btn--outline">Написати відгук</button>
                    </div>
                </div>

                <div v-else-if="tab === 'related'">
                    <h3 class="product-tabs__title">Супутні товари</h3>
                    <div class="cat-grid cat-grid--4">
                        <ProductCard v-for="p in related" :key="p.id" :product="p" />
                    </div>
                </div>
            </div>

            <RecentlyViewed :current="product" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import RecentlyViewed from '@/Components/RecentlyViewed.vue';
import { formatPrice as fmt, pluralUa } from '@/utils/format';
import { useCart } from '@/composables/useCart';

const { add, pending } = useCart();

const props = defineProps({
    product: Object,
    related: Array,
});

const qty = ref(1);
const activeImg = ref(0);
const tab = ref('desc');
const tabPanel = ref(null);

const images = computed(() => props.product.images ?? []);
// Завжди 4 мініатюри, як у дизайні: фото або заглушки
// Є фото — лише вони (мініатюри від двох фото); немає — 4 заглушки як у дизайні
const thumbs = computed(() => images.value.length
    ? (images.value.length > 1 ? images.value : [])
    : Array.from({ length: 4 }, () => null));

const isModified = computed(() => !!props.product.replacement);

// Кількість не більша за залишок на складі (ТЗ)
const maxQty = computed(() => props.product.availability === 'in_stock' && props.product.stock > 0 ? props.product.stock : null);
watch(maxQty, (m) => { if (m !== null && qty.value > m) qty.value = m; }, { immediate: true });

const tabs = computed(() => [
    { key: 'desc',    label: 'Опис' },
    { key: 'specs',   label: 'Характеристики' },
    ...(props.product.videos?.length ? [{ key: 'video', label: 'Відеоогляд' }] : []),
    { key: 'reviews', label: `Відгуки (${props.product.reviews ?? 0})` },
    ...(props.related?.length ? [{ key: 'related', label: 'Супутні товари' }] : []),
]);

function openTab(key) {
    tab.value = key;
    tabPanel.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function badgeClass(name) {
    return { 'Хіт': 'badge--hit', 'Акція': 'badge--sale', 'Новинка': 'badge--new' }[name] ?? '';
}
</script>
