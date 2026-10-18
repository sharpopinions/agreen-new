<template>
    <AccountLayout title="Каталог" subtitle="Замовляйте товари за вашими персональними цінами">
        <div class="cabinet-cats">
            <CategoryCard v-for="cat in categories" :key="cat.id" :category="cat" />
        </div>
        <Link href="/catalog" class="btn btn--outline cabinet__more">Увесь каталог →</Link>
    </AccountLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';
import CategoryCard from '@/Components/CategoryCard.vue';

defineProps({
    categories: { type: Array, default: () => [] },
});
</script>
