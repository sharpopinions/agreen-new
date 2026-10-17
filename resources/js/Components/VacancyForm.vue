<template>
    <LeadSuccess v-if="sent" title="Дякуємо за відгук!" text="Ми розглянемо вашу кандидатуру та зв'яжемось з вами." />
    <form v-else class="lead-form" novalidate @submit.prevent="submit({ vacancy: vacancy || form.position || null })">
        <Field v-model="form.name" label="Ім'я" required placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
        <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
        <Field v-model="form.email" label="Email" type="email" placeholder="email@gmail.com" autocomplete="email" :error="form.errors.email" />
        <Field v-if="!vacancy" v-model="form.position" label="Бажана посада" placeholder="Менеджер, спеціаліст..." :error="form.errors.vacancy" />
        <Field v-model="form.message" label="Про себе" textarea placeholder="Досвід, посилання на резюме..." :error="form.errors.message" />
        <Honeypot v-model="form.website" />
        <button class="btn btn--primary" type="submit" :disabled="form.processing">{{ vacancy ? 'Відгукнутись' : 'Надіслати резюме' }}</button>
    </form>
</template>

<script setup>
import Field from '@/Components/Form/Field.vue';
import Honeypot from '@/Components/Form/Honeypot.vue';
import LeadSuccess from '@/Components/Form/LeadSuccess.vue';
import { useLeadForm } from '@/composables/useLeadForm';

defineProps({
    vacancy: { type: String, default: null },
});

const { form, sent, submit } = useLeadForm('vacancy', { name: '', phone: '', email: '', position: '', message: '' });
</script>
