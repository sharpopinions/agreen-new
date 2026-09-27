<template>
    <AppLayout>
        <div class="cart-page">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/cart" class="breadcrumbs__item">Кошик</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">Оформлення замовлення</span>
            </nav>

            <h1 class="cart-page__title cart-page__title--mb">Оформлення замовлення</h1>

            <!-- Кроки -->
            <ol class="steps">
                <template v-for="(label, i) in steps" :key="label">
                    <li class="steps__item" :class="{ 'steps__item--done': step > i + 1, 'steps__item--active': step === i + 1 }">
                        <span class="steps__dot">
                            <svg v-if="step > i + 1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <template v-else>{{ i + 1 }}</template>
                        </span>
                        <span class="steps__label">{{ label }}</span>
                    </li>
                    <li v-if="i < steps.length - 1" class="steps__line" :class="{ 'steps__line--done': step > i + 1 }" aria-hidden="true"></li>
                </template>
            </ol>

            <div class="cart-page__layout cart-page__layout--checkout">
                <form class="checkout-card" novalidate @submit.prevent="next">

                    <!-- 1. Контактні дані -->
                    <div v-show="step === 1" class="checkout-step">
                        <h2 class="checkout-step__title">Контактні дані</h2>
                        <div class="checkout-note">
                            Постійний клієнт? Вхід для зареєстрованих покупців з'явиться найближчим часом — тоді дані заповнюватимуться автоматично.
                        </div>
                        <div class="checkout-grid">
                            <Field v-model="form.name" label="Ім'я" required placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
                            <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                            <Field v-model="form.email" label="Email" required type="email" placeholder="email@company.ua" autocomplete="email" :error="form.errors.email" />
                            <Field v-model="form.company" label="Компанія" placeholder="Назва компанії" autocomplete="organization" :error="form.errors.company" />
                        </div>
                        <template v-if="onlyPreorder">
                            <Field v-model="form.comment" label="Коментар" textarea placeholder="Додаткові побажання..." :error="form.errors.comment" />
                            <p class="checkout-legal">Натискаючи «Надіслати передзамовлення», ви погоджуєтесь з умовами публічної оферти.</p>
                            <div class="checkout-actions">
                                <button class="btn btn--primary" type="submit" :disabled="form.processing">Надіслати передзамовлення</button>
                            </div>
                        </template>
                        <div v-else class="checkout-actions">
                            <button class="btn btn--primary" type="submit">Далі — Доставка →</button>
                        </div>
                    </div>

                    <!-- 2. Доставка -->
                    <div v-show="step === 2" class="checkout-step">
                        <h2 class="checkout-step__title">Доставка</h2>
                        <div class="choice-list" role="radiogroup" aria-label="Спосіб доставки">
                            <label v-for="m in deliveryMethods" :key="m.id" class="choice" :class="{ 'choice--active': form.delivery === m.id }">
                                <input v-model="form.delivery" type="radio" name="delivery" :value="m.id" class="visually-hidden" />
                                <span class="choice__radio"></span>
                                <span>
                                    <span class="choice__title">{{ m.name }}</span>
                                    <span v-if="m.hint" class="choice__hint">{{ m.hint }}</span>
                                </span>
                            </label>
                        </div>
                        <p v-if="form.errors.delivery" class="field__error">{{ form.errors.delivery }}</p>
                        <div v-if="needsAddress" class="checkout-grid">
                            <Field v-model="form.city" label="Місто" required placeholder="Київ" autocomplete="address-level2" :error="form.errors.city" />
                            <Field v-model="form.address" label="Відділення / адреса" placeholder="Відділення №5" autocomplete="street-address" :error="form.errors.address" />
                        </div>
                        <div class="checkout-actions">
                            <button class="btn btn--secondary" type="button" @click="step = 1">← Назад</button>
                            <button class="btn btn--primary" type="submit">Далі — Оплата →</button>
                        </div>
                    </div>

                    <!-- 3. Оплата -->
                    <div v-show="step === 3" class="checkout-step">
                        <h2 class="checkout-step__title">Оплата</h2>
                        <div class="choice-list" role="radiogroup" aria-label="Спосіб оплати">
                            <label v-for="m in paymentMethods" :key="m.id" class="choice" :class="{ 'choice--active': form.payment === m.id }">
                                <input v-model="form.payment" type="radio" name="payment" :value="m.id" class="visually-hidden" />
                                <span class="choice__radio"></span>
                                <span>
                                    <span class="choice__title">{{ m.name }}</span>
                                    <span v-if="m.hint" class="choice__hint">{{ m.hint }}</span>
                                </span>
                            </label>
                        </div>
                        <p v-if="form.errors.payment" class="field__error">{{ form.errors.payment }}</p>
                        <Field v-model="form.comment" label="Коментар" textarea placeholder="Додаткові побажання..." :error="form.errors.comment" />
                        <p v-if="hasPreorder" class="checkout-legal">
                            Товари під замовлення буде оформлено окремим передзамовленням — без оплати та доставки.
                        </p>
                        <p class="checkout-legal">Натискаючи «Підтвердити замовлення», ви погоджуєтесь з умовами публічної оферти.</p>
                        <div class="checkout-actions">
                            <button class="btn btn--secondary" type="button" @click="step = 2">← Назад</button>
                            <button class="btn btn--primary" type="submit" :disabled="form.processing">Підтвердити замовлення</button>
                        </div>
                    </div>
                </form>

                <aside class="cart-summary">
                    <div class="cart-summary__title cart-summary__title--sm">Ваше замовлення</div>
                    <div class="checkout-items">
                        <div v-for="i in cart.items" :key="i.id" class="checkout-items__row">
                            <span class="checkout-items__name">
                                {{ i.name }} ×{{ i.quantity }}
                                <span v-if="i.preorder" class="cart-tag">Під замовлення</span>
                            </span>
                            <span class="checkout-items__sum">{{ fmt(i.total) }}</span>
                        </div>
                    </div>
                    <div class="cart-summary__total cart-summary__total--sm">
                        <span>Разом:</span><span>{{ fmt(cart.total) }}</span>
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Field from '@/Components/Form/Field.vue';
import { useCart } from '@/composables/useCart';
import { formatPrice as fmt } from '@/utils/format';

const props = defineProps({
    deliveryMethods: { type: Array, default: () => [] },
    paymentMethods:  { type: Array, default: () => [] },
});

const { cart } = useCart();

const hasPreorder  = computed(() => cart.value.items.some(i => i.preorder));
const onlyPreorder = computed(() => cart.value.items.length > 0 && cart.value.items.every(i => i.preorder));
const steps = computed(() => onlyPreorder.value ? ['Контактні дані'] : ['Контактні дані', 'Доставка', 'Оплата']);

const step = ref(1);

const form = useForm({
    name: '', phone: '', email: '', company: '',
    delivery: props.deliveryMethods[0]?.id ?? null, city: '', address: '',
    payment: props.paymentMethods[0]?.id ?? null, comment: '',
});

// Для самовивозу місто й відділення не потрібні
const needsAddress = computed(() =>
    props.deliveryMethods.find(m => m.id === form.delivery)?.requiresAddress ?? true);

// Клієнтська перевірка кроку перед переходом далі (сервер перевіряє все ще раз)
function validateStep(n) {
    form.clearErrors();
    const e = {};
    if (n === 1) {
        if (!form.name.trim())  e.name  = "Вкажіть ім'я.";
        if (!/^[0-9+()\-\s]{9,20}$/.test(form.phone.trim())) e.phone = 'Вкажіть телефон у форматі +380 XX XXX XX XX.';
        if (!/^\S+@\S+\.\S+$/.test(form.email.trim())) e.email = 'Вкажіть коректний email.';
    }
    if (n === 2 && !form.delivery) e.delivery = 'Оберіть спосіб доставки.';
    if (n === 2 && needsAddress.value && !form.city.trim()) e.city = 'Вкажіть місто.';
    if (n === 3 && !form.payment) e.payment = 'Оберіть спосіб оплати.';
    form.setError(e);
    return Object.keys(e).length === 0;
}

const stepOfField = { name: 1, phone: 1, email: 1, company: 1, delivery: 2, city: 2, address: 2, payment: 3, comment: 3 };

function next() {
    if (!validateStep(step.value)) return;
    if (step.value < steps.value.length) { step.value++; return; }

    form.post('/checkout', {
        preserveScroll: true,
        onError: (errors) => {
            // Повернутись на крок з першою помилкою
            const first = Object.keys(errors).map(k => stepOfField[k] ?? 1).sort()[0];
            step.value = onlyPreorder.value ? 1 : first;
        },
    });
}
</script>
