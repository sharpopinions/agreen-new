<template>
    <AccountLayout title="Навчання" subtitle="Курси навчального центру та найближчі події">
        <div v-if="courses.length" class="cab-cards">
            <Link v-for="c in courses" :key="c.title" :href="`/services/${c.slug}`" class="cab-card">
                <div class="cab-card__top">
                    <span class="cab-card__type">Курс</span>
                    <span v-if="c.level" class="cab-card__badge">{{ c.level }}</span>
                </div>
                <div class="cab-card__title">{{ c.title }}</div>
                <div class="cab-card__bottom">
                    <span v-if="c.duration" class="cab-card__meta">⏱ {{ c.duration }}</span>
                    <span v-if="c.price" class="cab-card__meta">{{ c.price }}</span>
                </div>
            </Link>
        </div>

        <h2 class="cabinet__h3">Найближчі події</h2>
        <div v-if="events.length" class="cab-cards">
            <Link v-for="e in events" :key="e.id" :href="`/blog/${e.slug}`" class="cab-card">
                <div class="cab-card__top">
                    <span class="cab-card__type">Подія</span>
                </div>
                <div class="cab-card__title">{{ e.title }}</div>
                <div class="cab-card__bottom">
                    <span class="cab-card__meta">📅 {{ e.starts }}</span>
                    <span class="cab-card__go">Детальніше →</span>
                </div>
            </Link>
        </div>
        <p v-else class="cab-note">Найближчих подій немає — стежте за оновленнями в <Link href="/blog">блозі</Link>.</p>
    </AccountLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';

defineProps({
    courses: { type: Array, default: () => [] },
    events:  { type: Array, default: () => [] },
});
</script>
