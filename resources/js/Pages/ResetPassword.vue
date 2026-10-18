<template>
    <AppLayout>
        <div class="info-page auth-page">
            <form class="auth-page__card auth-page__card--narrow auth__form" novalidate @submit.prevent="submit">
                <h1 class="auth__title">Новий пароль</h1>
                <Field v-model="form.email" label="Електронна пошта" type="email" autocomplete="email" :error="form.errors.email" />
                <Field v-model="form.password" label="Новий пароль" required type="password" placeholder="••••••••" autocomplete="new-password" :error="form.errors.password" />
                <Field v-model="form.password_confirmation" label="Повторити пароль" required type="password" placeholder="••••••••" autocomplete="new-password" />
                <button class="btn btn--primary btn--full" type="submit" :disabled="form.processing">Зберегти пароль</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Field from '@/Components/Form/Field.vue';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });
const submit = () => form.post('/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>
