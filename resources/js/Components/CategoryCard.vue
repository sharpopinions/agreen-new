<template>
    <article class="cat-card">
        <Link :href="href" class="cat-card__img img-ph" :aria-label="category.name" />
        <div class="cat-card__body">
            <Link :href="href" class="cat-card__name">{{ category.name }}</Link>
            <ul v-if="subs.length" class="cat-card__subs">
                <li v-for="sub in subs" :key="sub.id">
                    <Link :href="`/catalog/${sub.slug}`" class="cat-card__sub">{{ sub.name }}</Link>
                </li>
            </ul>
            <Link :href="href" class="link-more cat-card__more">Дивитися всі</Link>
        </div>
    </article>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
});

const href = computed(() => `/catalog/${props.category.slug}`);
const subs = computed(() => (props.category.children ?? []).slice(0, 3));
</script>
