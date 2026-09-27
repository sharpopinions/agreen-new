<template>
    <div>
        <TheHeader />
        <main>
            <slot />
        </main>
        <TheFooter />
        <CommandPalette />
    </div>
</template>

<script setup>
    import TheHeader from '@/Components/Layout/TheHeader.vue';
    import TheFooter from '@/Components/Layout/TheFooter.vue';
    import CommandPalette from '@/Components/Layout/CommandPalette.vue';
</script>
