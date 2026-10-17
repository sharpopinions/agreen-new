<template>
    <AppLayout>
        <!-- Claude Design → extra-pages.jsx: Page404 -->
        <div class="not-found">
            <div class="not-found__code">404</div>
            <div class="not-found__img"><ImgPlaceholder :h="80" label="🎨" seed="404" /></div>
            <h1 class="not-found__title">Здається, сторінку відправили на фарбування 😉…</h1>
            <p class="not-found__text">
                Посилання, за яким ви перейшли, більше неактуальне або сторінку видалено.
                Поверніться на головну сторінку або скористайтесь меню сайту.
            </p>
            <div class="not-found__actions">
                <Link href="/" class="btn btn--primary">На головну</Link>
                <Link href="/catalog" class="btn btn--outline">До каталогу</Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
</script>
