<template>
    <AppLayout>
        <!-- Claude Design → extra-pages.jsx: ContactsPage. Дані — адмінка «Сайт і контакти» -->
        <div class="info-page contacts-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Контакти</span>
            </nav>

            <h1 class="info-page__title">Контакти</h1>

            <div class="contacts-page__grid">
                <div class="contacts-page__info">
                    <div v-if="site.address" class="contact-card">
                        <div class="contact-card__icon">📍</div>
                        <div>
                            <div class="contact-card__label">Адреса</div>
                            <div class="contact-card__value">{{ site.address }}</div>
                        </div>
                    </div>
                    <div v-if="site.emails.length" class="contact-card">
                        <div class="contact-card__icon">✉</div>
                        <div>
                            <div class="contact-card__label">E-mail адреса</div>
                            <a v-for="e in site.emails" :key="e" :href="`mailto:${e}`" class="contact-card__value contact-card__link">{{ e }}</a>
                        </div>
                    </div>
                    <div v-if="site.schedule" class="contact-card">
                        <div class="contact-card__icon">🕐</div>
                        <div>
                            <div class="contact-card__label">Графік роботи</div>
                            <div class="contact-card__value contact-card__value--pre">{{ site.schedule }}</div>
                        </div>
                    </div>
                    <div v-for="d in site.departments" :key="d.name" class="contact-card">
                        <div class="contact-card__icon">📞</div>
                        <div>
                            <div class="contact-card__label">{{ d.name }}</div>
                            <a v-for="p in d.phones" :key="p.href" :href="p.href" class="contact-card__value contact-card__link">{{ p.label }}</a>
                        </div>
                    </div>
                </div>

                <div class="contacts-page__form">
                    <LeadSuccess v-if="sent" again @again="again" />
                    <form v-else class="lead-form" novalidate @submit.prevent="submit()">
                        <h3 class="lead-form__title">Написати нам</h3>
                        <Field v-model="form.name" label="Ім'я" required placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
                        <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                        <Field v-model="form.email" label="Email" type="email" placeholder="email@company.ua" autocomplete="email" :error="form.errors.email" />
                        <Field v-model="form.message" label="Повідомлення" textarea placeholder="Ваше запитання або пропозиція..." :error="form.errors.message" />
                        <Honeypot v-model="form.website" />
                        <button class="btn btn--primary" type="submit" :disabled="form.processing">Надіслати</button>
                    </form>
                </div>
            </div>

            <div class="contacts-page__map">
                <iframe v-if="site.mapUrl" :src="site.mapUrl" title="Карта" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                <ImgPlaceholder v-else :h="300" :label="`карта — ${site.address}`" seed="map" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Field from '@/Components/Form/Field.vue';
import Honeypot from '@/Components/Form/Honeypot.vue';
import LeadSuccess from '@/Components/Form/LeadSuccess.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import { useSite } from '@/composables/useSite';
import { useLeadForm } from '@/composables/useLeadForm';

const site = useSite();
const { form, sent, submit, again } = useLeadForm('contact', { name: '', phone: '', email: '', message: '' });
</script>
