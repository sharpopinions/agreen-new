<template>
    <AccountLayout title="Техпідтримка">
        <template #actions>
            <button type="button" class="btn btn--primary" @click="start('consultation')">+ Новий запит</button>
        </template>

        <div class="cab-kinds">
            <button
                v-for="k in kindCards"
                :key="k.key"
                type="button"
                class="cab-kind"
                :class="{ 'cab-kind--active': formOpen && form.kind === k.key }"
                @click="start(k.key)"
            >
                <span class="cab-kind__icon">{{ k.icon }}</span>
                <span class="cab-kind__title">{{ k.label }}</span>
                <span class="cab-kind__text">{{ k.text }}</span>
            </button>
        </div>

        <form v-if="formOpen" ref="formEl" class="cab-panel cab-support-form" novalidate @submit.prevent="submit">
            <div class="cab-panel__title">Новий запит: {{ kindCards.find(k => k.key === form.kind)?.label }}</div>
            <Field v-model="form.message" label="Опишіть питання" required textarea placeholder="Модель обладнання, що сталося, зручний час для звʼязку…" :error="form.errors.message" />
            <p v-if="form.errors.kind" class="field__error">{{ form.errors.kind }}</p>
            <div class="cab-panel__actions">
                <button class="btn btn--primary" type="submit" :disabled="form.processing">Надіслати</button>
                <button class="btn btn--ghost" type="button" @click="formOpen = false">Скасувати</button>
            </div>
        </form>

        <h2 class="cabinet__h3">Мої запити</h2>
        <div v-if="requests.length" class="cab-list">
            <div v-for="r in requests" :key="r.id" class="cab-list__row cab-ticket">
                <span class="cab-ticket__number">#{{ r.number }}</span>
                <span class="cab-ticket__text">
                    <strong>{{ r.kind }}</strong>
                    <span>{{ r.message }}</span>
                </span>
                <span class="cab-ticket__date">{{ r.date }}</span>
                <span class="ticket-pill" :class="`ticket-pill--${r.status}`">{{ r.label }}</span>
            </div>
        </div>
        <p v-else class="cab-note">Звернень ще немає. Оберіть тип запиту вище — менеджер звʼяжеться з вами.</p>
    </AccountLayout>
</template>

<script setup>
import { computed, nextTick, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';
import Field from '@/Components/Form/Field.vue';

const props = defineProps({
    kinds:    { type: Array, default: () => [] },
    requests: { type: Array, default: () => [] },
});

const meta = {
    consultation: { icon: '💬', text: 'Питання щодо товарів та послуг' },
    service:      { icon: '🔧', text: 'Ремонт та обслуговування обладнання' },
    specialist:   { icon: '📍', text: 'Виїзд майстра на обʼєкт' },
};
const kindCards = computed(() => props.kinds.map(k => ({ ...k, ...(meta[k.key] ?? {}) })));

const formOpen = ref(false);
const formEl = ref(null);
const form = useForm({ kind: 'consultation', message: '' });

async function start(kind) {
    form.kind = kind;
    formOpen.value = true;
    await nextTick();
    formEl.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    formEl.value?.querySelector('textarea')?.focus();
}

function submit() {
    form.post('/account/support', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('message');
            formOpen.value = false;
        },
    });
}
</script>
