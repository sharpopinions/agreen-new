<template>
    <Link :href="`/catalog/${category.slug}`" class="cat-card">
        <div class="cat-card__body">
            <div class="cat-card__name">{{ category.name }}</div>
            <div class="cat-card__count">{{ pluralUa(category.count ?? 0, 'товар', 'товари', 'товарів') }}</div>
            <ul class="cat-card__subs">
                <li v-for="sub in (category.children ?? []).slice(0, 3)" :key="sub.id" class="cat-card__sub">
                    {{ sub.name }}
                </li>
            </ul>
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { pluralUa } from '@/utils/format';

defineProps({
    category: Object,
});
</script>
