<template>
    <AccountLayout title="Документообіг">
        <div class="pill-tabs">
            <Link
                v-for="t in types"
                :key="t.key ?? 'all'"
                :href="t.key ? `/account/documents?type=${t.key}` : '/account/documents'"
                class="pill-tabs__item"
                :class="{ 'pill-tabs__item--active': type === t.key }"
                preserve-scroll
            >{{ t.label }}</Link>
        </div>

        <div v-if="documents.length" class="cab-list">
            <div v-for="d in documents" :key="d.id" class="cab-list__row cab-doc">
                <svg class="cab-doc__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /></svg>
                <span class="cab-doc__title">{{ d.title }}</span>
                <span class="cab-doc__type">{{ typeLabel[d.type] }}</span>
                <span class="cab-doc__date">{{ d.date }}</span>
                <a :href="d.url" class="btn btn--outline btn--sm">↓ {{ d.ext }}<template v-if="d.size"> · {{ size(d.size) }}</template></a>
            </div>
        </div>
        <div v-else class="cab-empty">
            <div class="cab-empty__icon">⊟</div>
            <p class="cab-empty__title">Документів поки немає</p>
            <p class="cab-empty__text">Рахунки, акти, накладні та договори зʼявляться тут, щойно менеджер їх додасть.</p>
        </div>
    </AccountLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';

defineProps({
    documents: { type: Array, default: () => [] },
    type:      { type: String, default: null },
});

const typeLabel = { invoice: 'Рахунок', act: 'Акт', waybill: 'Накладна', contract: 'Договір', other: 'Інше' };
const types = [
    { key: null, label: 'Усі' },
    { key: 'invoice', label: 'Рахунки' },
    { key: 'act', label: 'Акти' },
    { key: 'waybill', label: 'Накладні' },
    { key: 'contract', label: 'Договори' },
];

function size(bytes) {
    return bytes >= 1048576 ? `${(bytes / 1048576).toFixed(1)} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
}
</script>
