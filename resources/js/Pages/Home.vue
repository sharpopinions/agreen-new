<template>
    <div>
        <h1>{{ title }}</h1>
        <p>Inertia + Vue 3 працює!</p>
    </div>
</template>

<script setup>
defineProps({
    title: String,
});
</script>
