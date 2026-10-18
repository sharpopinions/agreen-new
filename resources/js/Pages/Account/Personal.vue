<template>
    <AccountLayout title="Персональні дані" subtitle="Ці дані автоматично підставляються при оформленні замовлення">
        <form class="cab-personal" novalidate @submit.prevent="save">
            <div class="cab-panel">
                <div class="cab-panel__title">Контактні дані</div>
                <div class="cab-panel__fields">
                    <Field v-model="form.name" label="Ім'я" required autocomplete="given-name" :error="form.errors.name" />
                    <Field v-model="form.last_name" label="Прізвище" required autocomplete="family-name" :error="form.errors.last_name" />
                    <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                    <div class="field">
                        <span class="field__label">Email</span>
                        <div class="field__control field__control--readonly">{{ profile.email }}</div>
                        <p class="cab-panel__hint">Email використовується для входу. Щоб змінити — напишіть у техпідтримку.</p>
                    </div>
                    <Field v-model="form.company" label="Компанія" placeholder="ТОВ «Назва»" autocomplete="organization" :error="form.errors.company" />
                    <Field v-model="form.company_code" label="ЄДРПОУ / ІПН" placeholder="12345678" :error="form.errors.company_code" />
                </div>
            </div>

            <div class="cab-personal__side">
                <div class="cab-panel">
                    <div class="cab-panel__title">Спосіб доставки за замовчуванням</div>
                    <div class="cab-radios">
                        <label v-for="d in deliveryMethods" :key="d.id" class="cab-radio">
                            <input v-model="form.delivery" type="radio" :value="d.id" name="delivery" />
                            <span class="cab-radio__dot"></span>{{ d.name }}
                        </label>
                    </div>
                    <div class="cab-panel__fields cab-panel__fields--one">
                        <Field v-model="form.city" label="Місто" placeholder="Київ" autocomplete="address-level2" :error="form.errors.city" />
                        <Field v-model="form.address" label="Відділення або адреса" placeholder="Відділення №1" autocomplete="street-address" :error="form.errors.address" />
                    </div>
                </div>
                <div class="cab-panel">
                    <div class="cab-panel__title">Спосіб оплати</div>
                    <div class="cab-radios">
                        <label v-for="p in paymentMethods" :key="p.id" class="cab-radio">
                            <input v-model="form.payment" type="radio" :value="p.id" name="payment" />
                            <span class="cab-radio__dot"></span>{{ p.name }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="cab-personal__actions">
                <button class="btn btn--primary" type="submit" :disabled="form.processing || !form.isDirty">Зберегти зміни</button>
            </div>
        </form>
    </AccountLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';
import Field from '@/Components/Form/Field.vue';

const props = defineProps({
    profile:         { type: Object, required: true },
    deliveryMethods: { type: Array, default: () => [] },
    paymentMethods:  { type: Array, default: () => [] },
});

const { email, ...editable } = props.profile;
const form = useForm(editable);

function save() {
    form.put('/account/personal', {
        preserveScroll: true,
        onSuccess: () => form.defaults(),
    });
}
</script>
