<template>
    <AppLayout>
        <!-- Claude Design → extra-pages2.jsx: VacanciesPage -->
        <div class="info-page vacancies-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/about" class="breadcrumbs__item">Про компанію</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Вакансії</span>
            </nav>

            <div class="services-page__banner">
                <ImgPlaceholder :h="260" label="банер вакансій — команда компанії" seed="vacancies" />
            </div>

            <div class="vacancies-intro">
                <div>
                    <h1 class="vacancies-intro__title">Приєднуйтесь до команди A-green</h1>
                    <p class="vacancies-intro__text">
                        Ми шукаємо талановитих та мотивованих людей, які хочуть розвиватись у сфері промислових товарів.
                        Пропонуємо конкурентну заробітну плату, соціальний пакет та можливості для кар'єрного зростання.
                    </p>
                    <div class="vacancies-perks">
                        <div v-for="[t, s] in perks" :key="t" class="vacancies-perks__item">
                            <div class="vacancies-perks__title">{{ t }}</div>
                            <div class="vacancies-perks__text">{{ s }}</div>
                        </div>
                    </div>
                </div>
                <div class="contacts-page__form">
                    <h3 class="lead-form__title vacancies-intro__form-title">Не знайшли підходящу вакансію?</h3>
                    <VacancyForm />
                </div>
            </div>

            <h2 class="block-heading">Відкриті вакансії</h2>
            <div v-if="vacancies.length" class="vacancy-list">
                <div v-for="v in vacancies" :key="v.id" class="vacancy" :class="{ 'vacancy--open': open === v.id }">
                    <button type="button" class="vacancy__head" :aria-expanded="open === v.id" @click="open = open === v.id ? null : v.id">
                        <span>
                            <span class="vacancy__title">{{ v.title }}</span>
                            <span class="vacancy__meta">{{ [v.department, v.city, v.employment].filter(Boolean).join(' · ') }}</span>
                        </span>
                        <svg class="vacancy__chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div v-if="open === v.id" class="vacancy__body">
                        <div>
                            <template v-if="v.description">
                                <div class="vacancy__subtitle">Про вакансію</div>
                                <p class="vacancy__text">{{ v.description }}</p>
                            </template>
                            <template v-if="v.requirements.length">
                                <div class="vacancy__subtitle">Вимоги</div>
                                <ul class="vacancy__req">
                                    <li v-for="r in v.requirements" :key="r">{{ r }}</li>
                                </ul>
                            </template>
                        </div>
                        <div>
                            <div class="vacancy__subtitle">Відгукнутись на вакансію</div>
                            <VacancyForm :vacancy="v.title" />
                        </div>
                    </div>
                </div>
            </div>
            <p v-else class="brands-page__empty">Зараз відкритих вакансій немає — надішліть резюме, і ми звʼяжемося, щойно зʼявиться посада.</p>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import VacancyForm from '@/Components/VacancyForm.vue';

defineProps({
    vacancies: { type: Array, default: () => [] },
});

const open = ref(null);

const perks = [
    ['Конкурентна ЗП', 'і квартальні бонуси'],
    ['Медичне страхування', 'для всіх співробітників'],
    ['Навчання', 'за рахунок компанії'],
    ['Дружня команда', 'і корпоративна культура'],
];
</script>
