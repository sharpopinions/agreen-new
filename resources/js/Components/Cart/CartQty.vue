<template>
    <div class="qty" :class="{ 'qty--sm': small }">
        <button class="qty__btn" :disabled="value <= 1 || disabled" aria-label="Менше" @click="$emit('change', value - 1)">−</button>
        <span class="qty__value">{{ value }}</span>
        <button class="qty__btn" :disabled="(max !== null && value >= max) || disabled" aria-label="Більше" @click="$emit('change', value + 1)">+</button>
    </div>
</template>

<script setup>
defineProps({
    value:    { type: Number, required: true },
    max:      { type: Number, default: null },
    small:    { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

defineEmits(['change']);
</script>
