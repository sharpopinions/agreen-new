<template>
    <!-- Пастка для ботів: поза екраном, без фокусу й автозаповнення -->
    <div class="hp-field" aria-hidden="true">
        <label>Website <input :value="modelValue" type="text" name="website" tabindex="-1" autocomplete="off" @input="$emit('update:modelValue', $event.target.value)" /></label>
    </div>
</template>

<script setup>
defineProps({ modelValue: { type: String, default: '' } });
defineEmits(['update:modelValue']);
</script>
