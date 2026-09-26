<template>
    <span class="stars" :aria-label="`Рейтинг ${value || 0} з 5`">
        <Icon
            v-for="i in 5"
            :key="i"
            name="star"
            :size="size"
            :stroke-width="0"
            :class="{ stars__on: i <= Math.round(value || 0) }"
            style="fill: currentColor"
        />
    </span>
</template>

<script setup>
import Icon from '@/Components/Icon.vue';

defineProps({
    value: { type: Number, default: 0 },
    size:  { type: Number, default: 18 },
});
</script>
