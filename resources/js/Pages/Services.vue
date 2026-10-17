<template>
    <AppLayout>
        <!-- Claude Design → extra-pages.jsx: ServicesPage -->
        <div class="info-page services-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Послуги</span>
            </nav>

            <div class="services-page__banner">
                <ImgPlaceholder :h="260" label="банер послуг — обладнання та спеціалісти" seed="services-banner" />
            </div>

            <h1 class="info-page__title services-page__title">Послуги</h1>
            <p class="services-page__intro">
                Ми надаємо повний спектр послуг для підтримки вашого бізнесу — від навчання персоналу до технічного обслуговування обладнання.
            </p>

            <div class="services-grid">
                <div v-for="(s, i) in services" :key="s.id" class="service-card">
                    <div class="service-card__n">{{ s.icon || String(i + 1).padStart(2, '0') }}</div>
                    <h3 class="service-card__title">{{ s.title }}</h3>
                    <p v-if="s.summary" class="service-card__text">{{ s.summary }}</p>
                    <ul v-if="s.details.length" class="service-card__details">
                        <li v-for="d in s.details" :key="d">{{ d }}</li>
                    </ul>
                    <Link :href="`/services/${s.slug}`" class="btn btn--outline btn--sm">Детальніше</Link>
                </div>
            </div>

            <template v-if="testimonials.length">
                <h2 class="block-heading">Відгуки клієнтів</h2>
                <div class="reviews-grid">
                    <div v-for="r in testimonials" :key="r.id" class="review-card">
                        <div class="review-card__stars" :aria-label="`Оцінка ${r.rating} з 5`">
                            <span v-for="n in 5" :key="n" :class="{ 'review-card__star--off': n > r.rating }">★</span>
                        </div>
                        <p class="review-card__text">«{{ r.text }}»</p>
                        <div class="review-card__author">{{ r.author }}</div>
                    </div>
                </div>
            </template>

            <section class="block-cta">
                <div>
                    <h3 class="block-cta__title">Залиште заявку на послугу</h3>
                    <p class="block-cta__text">Наш менеджер зв'яжеться з вами протягом 30 хвилин</p>
                </div>
                <button type="button" class="btn btn--primary" @click="modal = true">Залишити заявку</button>
            </section>

            <LeadModal v-model:open="modal" type="service" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import LeadModal from '@/Components/Form/LeadModal.vue';

defineProps({
    services:     { type: Array, default: () => [] },
    testimonials: { type: Array, default: () => [] },
});

const modal = ref(false);
</script>
