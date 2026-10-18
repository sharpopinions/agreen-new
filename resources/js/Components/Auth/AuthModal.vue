<template>
    <Teleport to="body">
        <div v-if="open" class="auth-modal" role="dialog" aria-modal="true" aria-label="Вхід до особистого кабінету" @keydown.esc="close">
            <div class="auth-modal__backdrop" @click="close"></div>
            <div class="auth-modal__panel" :class="{ 'auth-modal__panel--wide': step === 'role', 'auth-modal__panel--narrow': !['login', 'register', 'role'].includes(step) }">
                <button type="button" class="lead-modal__close" aria-label="Закрити" @click="close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <AuthForm v-model:step="step" @done="close" />
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import AuthForm from '@/Components/Auth/AuthForm.vue';
import { useAuthModal } from '@/composables/useAuthModal';

const { open, step, close } = useAuthModal();
</script>
