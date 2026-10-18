<template>
    <AppLayout>
        <div class="info-page auth-page">
            <div class="auth-page__card" :class="{ 'auth-page__card--wide': step === 'role' }">
                <AuthForm v-model:step="step" @done="router.visit('/account')" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AuthForm from '@/Components/Auth/AuthForm.vue';

// Незавершена реєстрація (email не підтверджено) — одразу крок з кодом
const page = usePage();
const step = ref(page.props.auth?.user && !page.props.auth.user.verified ? 'confirm' : 'login');
</script>
