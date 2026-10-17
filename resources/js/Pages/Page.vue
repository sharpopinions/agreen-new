<template>
    <AppLayout>
        <div class="info-page" :class="`info-page--${page.layout}`">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ page.title }}</span>
            </nav>

            <!-- Документ: зміст збоку, пронумеровані розділи (Оферта, Конфіденційність, Повернення) -->
            <div v-if="page.layout === 'document'" class="info-doc">
                <aside v-if="page.toc.length" class="info-doc__toc">
                    <div class="info-doc__toc-title">Зміст</div>
                    <a v-for="(t, i) in page.toc" :key="i" :href="`#section-${i + 1}`" class="info-doc__toc-link">{{ i + 1 }}. {{ t }}</a>
                </aside>
                <div class="info-doc__content">
                    <h1 class="info-page__title">{{ page.title }}</h1>
                    <PageBlock v-for="(block, i) in page.blocks" :key="i" :block="block" :number="sectionNumber(i)" />
                </div>
            </div>

            <!-- Блоки (Про нас, Доставка та оплата) -->
            <template v-else>
                <!-- Якщо сторінка починається з «Текст, фото й список» — його заголовок і є H1 (як у дизайні «Партнерам») -->
                <h1 v-if="!splitTitle" class="info-page__title" :class="{ 'info-page__title--center': hasHero }">{{ page.title }}</h1>
                <PageBlock v-for="(block, i) in page.blocks" :key="i" :block="block" :as-title="i === 0 && splitTitle" />
            </template>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageBlock from '@/Components/Blocks/PageBlock.vue';

const props = defineProps({
    page: { type: Object, required: true },
});

// Заголовок по центру, як у дизайні «Про компанію», коли сторінка починається з фото
const hasHero = computed(() => props.page.blocks[0]?.type === 'image');
const splitTitle = computed(() => props.page.blocks[0]?.type === 'split' && !!props.page.blocks[0]?.heading);

// Номер розділу документа — серед блоків «section»
function sectionNumber(index) {
    if (props.page.blocks[index].type !== 'section') return null;
    return props.page.blocks.slice(0, index + 1).filter(b => b.type === 'section').length;
}
</script>
