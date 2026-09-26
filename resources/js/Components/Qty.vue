<template>
    <span class="qty">
        <button class="qty__btn" type="button" aria-label="Менше" :disabled="modelValue <= min" @click="set(modelValue - 1)">
            <Icon name="minus" :size="size" :stroke-width="1" />
        </button>
        <span class="qty__value">{{ modelValue }}</span>
        <button class="qty__btn" type="button" aria-label="Більше" @click="set(modelValue + 1)">
            <Icon name="plus" :size="size" :stroke-width="1" />
        </button>
    </span>
</template>

<script setup>
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    modelValue: { type: Number, default: 1 },
    min:        { type: Number, default: 1 },
    size:       { type: Number, default: 18 },
});

const emit = defineEmits(['update:modelValue']);

const set = (v) => emit('update:modelValue', Math.max(props.min, v));
</script>
