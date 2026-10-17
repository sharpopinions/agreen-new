<template>
    <!-- Claude Design → ContactsPage / ThankFormPage -->
    <div class="lead-success" role="status">
        <div class="lead-success__icon">✅</div>
        <h3 class="lead-success__title">{{ title }}</h3>
        <p class="lead-success__text">{{ text }}</p>
        <button v-if="again" type="button" class="btn btn--outline btn--sm" @click="$emit('again')">Надіслати ще</button>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, default: 'Повідомлення надіслано!' },
    text:  { type: String, default: "Ми зв'яжемось з вами найближчим часом." },
    again: { type: Boolean, default: false },
});
defineEmits(['again']);
</script>
