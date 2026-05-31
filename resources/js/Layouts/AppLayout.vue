<template>
    <div>
        <TheHeader />
        <main>
            <slot />
        </main>
        <TheFooter />
    </div>
</template>

<script setup>
    import TheHeader from '@/Components/Layout/TheHeader.vue';
    import TheFooter from '@/Components/Layout/TheFooter.vue';
</script>
