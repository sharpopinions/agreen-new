<template>
    <!-- Розділ документа -->
    <section v-if="block.type === 'section'" :id="number ? `section-${number}` : undefined" class="block-section">
        <h2 class="block-section__title">{{ number ? `${number}. ` : '' }}{{ block.title }}</h2>
        <div class="rich-text" v-html="block.html"></div>
    </section>

    <!-- Текст -->
    <section v-else-if="block.type === 'text'" class="block-text rich-text" v-html="block.html"></section>

    <!-- Фото на всю ширину (без фото — заглушка, як у дизайні) -->
    <div v-else-if="block.type === 'image'" class="block-image">
        <img v-if="block.url" :src="block.url" :alt="block.alt" loading="lazy" />
        <ImgPlaceholder v-else :h="320" :label="block.alt || 'фото'" :seed="block.alt || 'page'" />
    </div>

    <!-- Текст, фото й список з галочками -->
    <section v-else-if="block.type === 'split'" class="block-split">
        <div>
            <component :is="asTitle ? 'h1' : 'h2'" v-if="block.heading" class="block-heading block-split__title">{{ block.heading }}</component>
            <div v-if="block.html" class="rich-text" v-html="block.html"></div>
            <template v-if="block.items.length">
                <div v-if="block.listHeading" class="block-lead__perks-title">{{ block.listHeading }}</div>
                <ul class="check-list">
                    <li v-for="item in block.items" :key="item" class="check-list__item">{{ item }}</li>
                </ul>
            </template>
        </div>
        <div class="block-split__media">
            <img v-if="block.image" :src="block.image" :alt="block.alt" loading="lazy" />
            <ImgPlaceholder v-else :h="420" :label="block.alt || 'фото'" :seed="block.heading || 'split'" />
        </div>
    </section>

    <!-- Форма заявки -->
    <LeadFormBlock v-else-if="block.type === 'lead_form'" :block="block" />

    <!-- Текст і цифри -->
    <section v-else-if="block.type === 'intro_stats'" class="block-intro">
        <div>
            <h2 v-if="block.heading" class="block-heading">{{ block.heading }}</h2>
            <div class="rich-text" v-html="block.html"></div>
        </div>
        <div v-if="block.stats.length" class="block-intro__stats">
            <div v-for="s in block.stats" :key="s.value + s.label" class="block-stat">
                <div class="block-stat__value">{{ s.value }}</div>
                <div class="block-stat__label">{{ s.label }}</div>
            </div>
        </div>
    </section>

    <!-- Переваги -->
    <section v-else-if="block.type === 'features'" class="block-features">
        <h2 v-if="block.heading" class="block-heading">{{ block.heading }}</h2>
        <div class="block-features__grid">
            <div v-for="(item, i) in block.items" :key="i" class="block-feature">
                <div class="block-feature__n">{{ String(i + 1).padStart(2, '0') }}</div>
                <div class="block-feature__title">{{ item.title }}</div>
                <div v-if="item.text" class="block-feature__text">{{ item.text }}</div>
            </div>
        </div>
    </section>

    <!-- Колонки зі списками (Доставка / Оплата) -->
    <section v-else-if="block.type === 'columns'" class="block-columns" :style="{ '--cols': block.columns.length }">
        <div v-for="(col, i) in block.columns" :key="i" class="block-column">
            <h2 class="block-column__title">{{ col.heading }}</h2>
            <div v-for="(item, j) in col.items" :key="j" class="block-column__item">
                <div class="block-column__item-title">{{ item.title }}</div>
                <div v-if="item.text" class="block-column__item-text">{{ item.text }}</div>
            </div>
        </div>
    </section>

    <!-- Наші бренди -->
    <section v-else-if="block.type === 'brands' && block.brands.length" class="block-brands">
        <h2 v-if="block.heading" class="block-heading">{{ block.heading }}</h2>
        <div class="block-brands__grid">
            <Link v-for="b in block.brands" :key="b.id" :href="`/brands/${b.slug}`" class="block-brand">
                <span class="block-brand__logo">
                    <img v-if="b.logo" :src="b.logo" :alt="b.name" loading="lazy" />
                    <template v-else>{{ b.name.charAt(0) }}</template>
                </span>
                <span class="block-brand__name">{{ b.name }}</span>
            </Link>
        </div>
    </section>

    <!-- Заклик до дії -->
    <section v-else-if="block.type === 'cta'" class="block-cta">
        <div>
            <h3 class="block-cta__title">{{ block.heading }}</h3>
            <p v-if="block.text" class="block-cta__text">{{ block.text }}</p>
        </div>
        <div v-if="block.buttons.length" class="block-cta__actions">
            <Link
                v-for="(btn, i) in block.buttons"
                :key="i"
                :href="btn.url"
                class="btn"
                :class="btn.style === 'outline' ? 'btn--outline' : 'btn--primary'"
            >{{ btn.label }}</Link>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import LeadFormBlock from '@/Components/Blocks/LeadFormBlock.vue';

defineProps({
    block:  { type: Object, required: true },
    number: { type: Number, default: null },
    asTitle: { type: Boolean, default: false },
});
</script>
