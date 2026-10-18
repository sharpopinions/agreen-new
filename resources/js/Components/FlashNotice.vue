<template>
    <Transition name="flash-notice">
        <div v-if="text" class="flash-notice" role="status" @click="text = ''">
            <span class="flash-notice__icon">✓</span>{{ text }}
        </div>
    </Transition>
</template>

<script setup>
// Коротке повідомлення після дії (flash notice з сервера): «Зміни збережено» тощо
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const text = ref('');
let timer;

watch(() => page.props.flash?.notice, (value) => {
    if (!value) return;
    text.value = value;
    clearTimeout(timer);
    timer = setTimeout(() => (text.value = ''), 4000);
}, { immediate: true });
</script>
