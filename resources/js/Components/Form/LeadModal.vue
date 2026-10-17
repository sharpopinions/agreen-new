<template>
    <Teleport to="body">
        <div v-if="open" class="lead-modal" role="dialog" aria-modal="true" :aria-label="config.title" @keydown.esc="close">
            <div class="lead-modal__backdrop" @click="close"></div>
            <div ref="panel" class="lead-modal__panel" tabindex="-1">
                <button class="lead-modal__close" type="button" aria-label="Закрити" @click="close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>

                <LeadSuccess v-if="sent" :title="config.successTitle" :text="config.successText" />
                <form v-else class="lead-form" novalidate @submit.prevent="send">
                    <h3 class="lead-form__title">{{ config.title }}</h3>
                    <p v-if="product" class="lead-form__product">{{ product.name }} <span>· арт. {{ product.sku }}</span></p>
                    <p v-if="config.hint" class="lead-form__hint">{{ config.hint }}</p>

                    <Field v-if="config.fields.includes('name')" v-model="form.name" label="Ім'я" :required="config.required.includes('name')" placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
                    <Field v-if="config.fields.includes('phone')" v-model="form.phone" label="Телефон" :required="config.required.includes('phone')" type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                    <Field v-if="config.fields.includes('email')" v-model="form.email" label="Email" :required="config.required.includes('email')" type="email" placeholder="email@company.ua" autocomplete="email" :error="form.errors.email" />
                    <Field v-if="config.fields.includes('company')" v-model="form.company" label="Компанія" placeholder="Назва компанії" autocomplete="organization" :error="form.errors.company" />
                    <Field v-if="config.fields.includes('quantity')" v-model="form.quantity" label="Кількість, шт." type="number" placeholder="1" :error="form.errors.quantity" />
                    <Field v-if="config.fields.includes('message')" v-model="form.message" label="Коментар" textarea placeholder="Додаткові побажання..." :error="form.errors.message" />
                    <p v-if="form.errors.product_id" class="field__error">{{ form.errors.product_id }}</p>
                    <Honeypot v-model="form.website" />

                    <button class="btn btn--primary btn--full" type="submit" :disabled="form.processing">{{ config.submit }}</button>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import Field from '@/Components/Form/Field.vue';
import Honeypot from '@/Components/Form/Honeypot.vue';
import LeadSuccess from '@/Components/Form/LeadSuccess.vue';
import { useLeadForm } from '@/composables/useLeadForm';

// Форми зі сторінки товару: швидке замовлення, повідомлення про надходження, запит гуртової ціни
const CONFIG = {
    quick_order: {
        title: 'Швидке замовлення', submit: 'Замовити',
        hint: 'Залиште телефон — менеджер передзвонить і оформить замовлення.',
        fields: ['name', 'phone', 'quantity'], required: ['name', 'phone'],
        successTitle: 'Дякуємо! Замовлення прийнято', successText: 'Менеджер зателефонує вам найближчим часом.',
    },
    stock_notify: {
        title: 'Повідомити про надходження', submit: 'Повідомити мене',
        hint: 'Напишемо, щойно товар зʼявиться на складі. Достатньо телефону або email.',
        fields: ['name', 'phone', 'email'], required: [],
        successTitle: 'Готово!', successText: 'Ми повідомимо вас, коли товар надійде.',
    },
    price_request: {
        title: 'Запит на гуртову ціну', submit: 'Надіслати запит',
        hint: 'Вкажіть потрібну кількість — підготуємо персональну пропозицію.',
        fields: ['name', 'phone', 'email', 'company', 'quantity', 'message'], required: ['name', 'phone'],
        successTitle: 'Запит надіслано!', successText: 'Менеджер підготує пропозицію і звʼяжеться з вами.',
    },
};

const props = defineProps({
    type:    { type: String, required: true },
    product: { type: Object, default: null },
    open:    { type: Boolean, default: false },
});
const emit = defineEmits(['update:open']);

const config = computed(() => CONFIG[props.type]);
const { form, sent, submit, again } = useLeadForm(props.type, { name: '', phone: '', email: '', company: '', quantity: '', message: '' });
const panel = ref(null);

watch(() => props.open, async (isOpen) => {
    if (isOpen) {
        again();
        await nextTick();
        panel.value?.focus();
    }
}, { immediate: true });

function send() {
    submit({ product_id: props.product?.id, quantity: form.quantity || null });
}

function close() {
    emit('update:open', false);
}
</script>
