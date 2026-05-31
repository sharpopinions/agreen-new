<template>
    <div class="cat-card">
        <div class="cat-card__body">
            <div class="cat-card__name">{{ category.name }}</div>
            <div class="cat-card__count">{{ category.count.toLocaleString('uk-UA') }} товарів</div>
            <ul class="cat-card__subs">
                <li v-for="sub in category.subs.slice(0, 3)" :key="sub" class="cat-card__sub">
                    {{ sub }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
defineProps({
    category: Object,
});
</script>
