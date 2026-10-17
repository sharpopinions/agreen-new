<template>
    <Link :href="`/blog/${post.slug}`" class="blog-card" :class="{ 'blog-card--sm': small }">
        <span class="blog-card__img">
            <img v-if="post.image" :src="post.image" :alt="post.title" loading="lazy" />
            <ImgPlaceholder v-else :h="small ? 120 : 160" :label="small ? '' : 'фото'" :seed="post.slug" />
            <span v-if="post.type === 'event'" class="blog-card__badge">🎟️ Подія</span>
        </span>
        <span class="blog-card__body">
            <span v-if="!small" class="blog-card__meta">
                <span class="blog-card__cat">{{ post.category }}</span>
                <span>{{ post.read }} хв читання</span>
            </span>
            <span class="blog-card__title">{{ post.title }}</span>
            <time v-if="!small" class="blog-card__date" :datetime="post.iso">{{ post.date }}</time>
        </span>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';

defineProps({
    post:  { type: Object, required: true },
    small: { type: Boolean, default: false },
});
</script>
