<template>
    <div class="field" :class="{ 'field--error': error }">
        <label :for="id" class="field__label">
            {{ label }}<span v-if="required" class="field__req" aria-hidden="true"> *</span>
        </label>
        <textarea
            v-if="textarea"
            :id="id"
            class="field__control field__control--textarea"
            :value="modelValue"
            :placeholder="placeholder"
            :aria-invalid="!!error"
            :aria-describedby="error ? `${id}-err` : undefined"
            @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>
        <input
            v-else
            :id="id"
            class="field__control"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :required="required"
            :aria-invalid="!!error"
            :aria-describedby="error ? `${id}-err` : undefined"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" :id="`${id}-err`" class="field__error">{{ error }}</p>
    </div>
</template>

<script setup>
// Поле форми з Claude Design (components.jsx → Field)
const props = defineProps({
    modelValue:   { type: String, default: '' },
    label:        { type: String, required: true },
    type:         { type: String, default: 'text' },
    placeholder:  { type: String, default: '' },
    autocomplete: { type: String, default: undefined },
    required:     { type: Boolean, default: false },
    textarea:     { type: Boolean, default: false },
    error:        { type: String, default: '' },
});

defineEmits(['update:modelValue']);

const id = `f-${Math.random().toString(36).slice(2, 9)}`;
</script>
