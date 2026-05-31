<template>
    <AppLayout>
        <div style="padding: 2rem">
            <h1>{{ title }}</h1>
            <p>Inertia + Vue 3 + Layout працює!</p>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';

    defineProps({
        title: String,
    });
</script>

