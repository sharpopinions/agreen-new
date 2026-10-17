<template>
    <AppLayout>
        <!-- Claude Design → extra-pages7.jsx: StudyCenterPage (шаблон сторінки послуги) -->
        <div class="info-page service-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/services" class="breadcrumbs__item">Послуги</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ service.title }}</span>
            </nav>

            <header class="service-hero" :class="{ 'service-hero--stats': service.stats.length }">
                <div>
                    <span class="service-hero__badge">Послуги · {{ service.title }}</span>
                    <h1 class="service-hero__title">{{ service.title }}</h1>
                    <p v-if="service.summary" class="service-hero__text">{{ service.summary }}</p>
                    <div class="service-hero__actions">
                        <button type="button" class="btn btn--primary" @click="toForm()">{{ service.courses.length ? 'Записатись на курс' : 'Залишити заявку' }}</button>
                        <Link href="/contacts" class="btn btn--outline">Контакти</Link>
                    </div>
                </div>
                <div v-if="service.stats.length" class="service-hero__stats">
                    <div v-for="s in service.stats" :key="s.label" class="service-hero__stat">
                        <span class="service-hero__stat-label">{{ s.label }}</span>
                        <span class="service-hero__stat-value">{{ s.value }}</span>
                    </div>
                </div>
            </header>

            <template v-if="service.benefits.length">
                <h2 class="block-heading">Чому обирають нас</h2>
                <div class="service-benefits">
                    <div v-for="b in service.benefits" :key="b.title" class="service-benefit">
                        <div v-if="b.icon" class="service-benefit__icon">{{ b.icon }}</div>
                        <h3 class="service-benefit__title">{{ b.title }}</h3>
                        <p v-if="b.text" class="service-benefit__text">{{ b.text }}</p>
                    </div>
                </div>
            </template>

            <template v-if="service.courses.length">
                <h2 class="block-heading">Програми навчання</h2>
                <div class="course-list">
                    <div v-for="(c, i) in service.courses" :key="c.title" class="course-list__row">
                        <div>
                            <div class="course-list__n">Курс №{{ i + 1 }}</div>
                            <div class="course-list__title">{{ c.title }}</div>
                        </div>
                        <div class="course-list__dur">{{ c.duration }}</div>
                        <span v-if="c.level" class="promo-tag course-list__level">{{ c.level }}</span>
                        <span v-else></span>
                        <div class="course-list__price">{{ c.price }}</div>
                        <button type="button" class="btn btn--outline btn--sm" @click="toForm(c.title)">Записатись</button>
                    </div>
                </div>
            </template>

            <div v-if="service.body" class="rich-text service-page__body" v-html="service.body"></div>

            <section ref="formBox" class="block-lead__box service-page__form">
                <div>
                    <h2 class="block-heading">{{ service.courses.length ? 'Записатись на навчання' : 'Замовити послугу' }}</h2>
                    <p class="service-page__form-text">
                        Залиште заявку і наш менеджер зв'яжеться з вами протягом одного робочого дня, щоб обговорити деталі.
                    </p>
                    <ul v-if="service.details.length" class="check-list">
                        <li v-for="d in service.details" :key="d" class="check-list__item">{{ d }}</li>
                    </ul>
                </div>
                <div>
                    <LeadSuccess v-if="sent" title="Заявку отримано!" text="Менеджер зателефонує вам найближчим часом." again @again="again" />
                    <form v-else class="lead-form" novalidate @submit.prevent="submit({ service: service.title, course: form.course || null })">
                        <Field v-model="form.name" label="Ім'я" required placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
                        <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                        <Field v-model="form.company" label="Компанія" placeholder="Назва компанії (необов'язково)" autocomplete="organization" :error="form.errors.company" />
                        <div v-if="service.courses.length" class="field">
                            <label for="course" class="field__label">Курс</label>
                            <select id="course" v-model="form.course" class="field__control">
                                <option value="">Який курс цікавить?</option>
                                <option v-for="c in service.courses" :key="c.title" :value="c.title">{{ c.title }}</option>
                            </select>
                        </div>
                        <Field v-else v-model="form.message" label="Коментар" textarea placeholder="Опишіть задачу..." :error="form.errors.message" />
                        <Honeypot v-model="form.website" />
                        <button class="btn btn--primary" type="submit" :disabled="form.processing">Надіслати заявку</button>
                    </form>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Field from '@/Components/Form/Field.vue';
import Honeypot from '@/Components/Form/Honeypot.vue';
import LeadSuccess from '@/Components/Form/LeadSuccess.vue';
import { useLeadForm } from '@/composables/useLeadForm';

defineProps({
    service: { type: Object, required: true },
});

const { form, sent, submit, again } = useLeadForm('service', { name: '', phone: '', company: '', course: '', message: '' });
const formBox = ref(null);

function toForm(course = null) {
    if (course) form.course = course;
    formBox.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
