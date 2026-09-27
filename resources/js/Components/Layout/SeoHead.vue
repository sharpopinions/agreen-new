<template>
    <Head :title="seo.title">
        <meta v-if="seo.description" head-key="description" name="description" :content="seo.description" />
        <meta head-key="og:title" property="og:title" :content="seo.title" />
        <meta v-if="seo.description" head-key="og:description" property="og:description" :content="seo.description" />
        <meta v-if="seo.image" head-key="og:image" property="og:image" :content="seo.image" />
        <meta v-if="seo.noindex" head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>
</template>

<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

// prop `seo` задає контролер (App\Support\Seo); без нього — назва сайту
const page = usePage();
const seo = computed(() => page.props.seo ?? { title: 'A-green' });
</script>
