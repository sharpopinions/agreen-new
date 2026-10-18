<template>
    <AppLayout>
        <div class="info-page">
            <h1 class="info-page__title">Вітаємо, {{ user.name }}! 👋</h1>
            <p v-if="user.requestedRole" class="auth__notice">Заявку на бізнес-умови отримано — менеджер звʼяжеться з вами після перевірки.</p>
            <button type="button" class="btn btn--outline" @click="router.post('/auth/logout')">Вийти</button>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>
