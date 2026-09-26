<template>
    <AppLayout>
        <div class="container">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__item breadcrumbs__item--active">Каталог</span>
            </nav>

            <p class="catalog-intro">
                Оберіть категорію, щоб швидко знайти потрібний товар. Ми пропонуємо широкий асортимент продукції
                для різних потреб — від базових матеріалів до професійних рішень. Ціни на сайті вказані згідно
                рекомендованих виробниками прайс-листів. Для вас — гнучка система знижок!
            </p>

            <form class="catalog-search" role="search" @submit.prevent="search">
                <Icon name="search" :size="20" class="catalog-search__icon" />
                <input
                    v-model="query"
                    class="catalog-search__input"
                    type="search"
                    placeholder="Введіть назву товару або категорії"
                    aria-label="Пошук у каталозі"
                />
            </form>

            <div class="catalog-categories">
                <CategoryCard v-for="cat in categories" :key="cat.id" :category="cat" />
            </div>

            <div class="section__more">
                <Link href="/catalog/all" class="btn btn--primary btn--wide">Дивитись усі товари</Link>
            </div>

            <section v-if="brands.length" class="section">
                <h2 class="section__title">Популярні бренди</h2>
                <div class="home-brands">
                    <Link
                        v-for="brand in brands.slice(0, 5)"
                        :key="brand.id"
                        :href="`/catalog/all?brand[]=${brand.id}`"
                        class="home-brands__item"
                    >{{ brand.name }}</Link>
                </div>
                <div class="section__more">
                    <Link href="/brands" class="btn btn--primary btn--wide">Дивитись усі бренди</Link>
                </div>
            </section>

            <section v-if="hits.length" class="section">
                <h2 class="section__title">Хіти продажів</h2>
                <ProductCarousel :products="hits" />
            </section>

            <div class="catalog-seo">
                <p>
                    Ми зібрали у нашому каталозі все необхідне для комплексного обслуговування підприємств
                    малярно-кузовного ремонту, промислових та виробничих підприємств, а також підприємств, що
                    використовують гігієнічну продукцію, щоб ви могли швидко підібрати потрібні рішення.
                </p>
                <p>
                    Продукція представлена від провідних світових брендів та перевірених постачальників. Купуючи
                    у нас, ви отримуєте якість, офіційну гарантію та професійну підтримку.
                </p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import CategoryCard from '@/Components/CategoryCard.vue';
import ProductCarousel from '@/Components/ProductCarousel.vue';

defineProps({
    categories: Array,
    brands:     Array,
    hits:       Array,
});

const query = ref('');

// Пошук за назвою або артикулом — у списку всіх товарів
function search() {
    const q = query.value.trim();
    if (q) router.get('/catalog/all', { q });
}
</script>
