<template>
    <AppLayout>
        <!-- Claude Design → extra-pages.jsx: BlogPage, extra-pages9.jsx: BlogFilteredPage -->
        <div class="info-page blog-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <template v-if="currentName">
                    <Link href="/blog" class="breadcrumbs__item">Блог</Link>
                    <span class="breadcrumbs__sep">›</span>
                    <span class="breadcrumbs__item breadcrumbs__item--active">{{ currentName }}</span>
                </template>
                <span v-else class="breadcrumbs__item breadcrumbs__item--active">Блог</span>
            </nav>

            <h1 class="info-page__title info-page__title--center blog-page__title">Блог</h1>

            <div v-if="categories.length" class="pill-tabs pill-tabs--center">
                <Link href="/blog" class="pill-tabs__item" :class="{ 'pill-tabs__item--active': !category }" preserve-scroll>Всі</Link>
                <Link
                    v-for="c in categories"
                    :key="c.slug"
                    :href="`/blog?category=${c.slug}`"
                    class="pill-tabs__item"
                    :class="{ 'pill-tabs__item--active': category === c.slug }"
                    preserve-scroll
                >{{ c.name }}</Link>
            </div>

            <div v-if="items.length" class="blog-grid">
                <BlogCard v-for="post in items" :key="post.id" :post="post" />
            </div>
            <p v-else class="brands-page__empty">Записів поки немає.</p>

            <div v-if="posts.next_page_url" class="blog-page__more">
                <button type="button" class="btn btn--outline" :disabled="loading" @click="loadMore">Завантажити ще</button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BlogCard from '@/Components/BlogCard.vue';

const props = defineProps({
    posts:      { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    category:   { type: String, default: null },
});

const currentName = computed(() => props.categories.find(c => c.slug === props.category)?.name);

// «Завантажити ще»: наступна сторінка дописується до вже показаних записів
const items   = ref([...props.posts.data]);
const loading = ref(false);

watch(() => props.posts, (posts) => {
    items.value = posts.current_page > 1 ? [...items.value, ...posts.data] : [...posts.data];
});

function loadMore() {
    loading.value = true;
    router.get(props.posts.next_page_url, {}, {
        preserveScroll: true,
        preserveState: true,
        only: ['posts'],
        onFinish: () => { loading.value = false; },
    });
}
</script>
