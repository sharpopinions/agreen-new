<template>
    <div>
        <SeoHead />
        <TheHeader />
        <main>
            <slot />
        </main>
        <TheFooter />
        <CommandPalette />
        <CartAddedModal />
        <CartDrawer />
        <AuthModal />
        <FlashNotice />
    </div>
</template>

<script setup>
    import SeoHead from '@/Components/Layout/SeoHead.vue';
    import TheHeader from '@/Components/Layout/TheHeader.vue';
    import TheFooter from '@/Components/Layout/TheFooter.vue';
    import CommandPalette from '@/Components/Layout/CommandPalette.vue';
    import CartAddedModal from '@/Components/Cart/CartAddedModal.vue';
    import CartDrawer from '@/Components/Cart/CartDrawer.vue';
    import AuthModal from '@/Components/Auth/AuthModal.vue';
    import FlashNotice from '@/Components/FlashNotice.vue';
</script>
