<template>
    <AccountLayout title="Налаштування">
        <div class="cab-settings">
            <div class="cab-panel">
                <div class="cab-panel__title">Сповіщення</div>
                <div v-for="item in toggles" :key="item.key" class="cab-toggle-row">
                    <div>
                        <div class="cab-toggle-row__title">{{ item.title }}</div>
                        <div class="cab-toggle-row__text">{{ item.text }}</div>
                    </div>
                    <button
                        type="button"
                        role="switch"
                        class="toggle"
                        :class="{ 'toggle--on': notify[item.key] }"
                        :aria-checked="notify[item.key]"
                        :aria-label="item.title"
                        :disabled="notify.processing"
                        @click="flip(item.key)"
                    ><span class="toggle__knob"></span></button>
                </div>
            </div>

            <div class="cab-settings__actions">
                <button type="button" class="btn btn--outline" @click="panel = panel === 'password' ? null : 'password'">Змінити пароль</button>
                <button type="button" class="btn btn--ghost cab-danger" @click="panel = panel === 'delete' ? null : 'delete'">Видалити акаунт</button>
            </div>

            <form v-if="panel === 'password'" class="cab-panel" novalidate @submit.prevent="savePassword">
                <div class="cab-panel__title">Зміна пароля</div>
                <div class="cab-panel__fields cab-panel__fields--one">
                    <Field v-model="password.current_password" label="Поточний пароль" type="password" required autocomplete="current-password" :error="password.errors.current_password" />
                    <Field v-model="password.password" label="Новий пароль" type="password" required placeholder="Мінімум 8 символів" autocomplete="new-password" :error="password.errors.password" />
                    <Field v-model="password.password_confirmation" label="Повторіть новий пароль" type="password" required autocomplete="new-password" />
                </div>
                <div class="cab-panel__actions">
                    <button class="btn btn--primary" type="submit" :disabled="password.processing">Зберегти пароль</button>
                </div>
            </form>

            <form v-if="panel === 'delete'" class="cab-panel cab-panel--danger" novalidate @submit.prevent="destroy">
                <div class="cab-panel__title">Видалення акаунту</div>
                <p class="cab-panel__hint">Профіль, документи й кешбек буде видалено без можливості відновлення. Історія замовлень залишиться у магазині без привʼязки до вас.</p>
                <div class="cab-panel__fields cab-panel__fields--one">
                    <Field v-model="del.password" label="Пароль для підтвердження" type="password" required autocomplete="current-password" :error="del.errors.password" />
                </div>
                <div class="cab-panel__actions">
                    <button class="btn btn--primary cab-danger-btn" type="submit" :disabled="del.processing || !del.password">Видалити назавжди</button>
                </div>
            </form>
        </div>
    </AccountLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';
import Field from '@/Components/Form/Field.vue';

const props = defineProps({
    notifications: { type: Object, required: true },
});

const toggles = [
    { key: 'email_orders', title: 'Email-сповіщення', text: 'Отримувати листи про статус замовлень' },
    { key: 'sms_orders',   title: 'SMS-сповіщення',   text: 'Сповіщення про доставку по SMS' },
    { key: 'newsletter',   title: 'Розсилка акцій',   text: 'Новини, акції та спецпропозиції' },
];

const notify = useForm({ ...props.notifications });
const panel = ref(null);
const password = useForm({ current_password: '', password: '', password_confirmation: '' });
const del = useForm({ password: '' });

function flip(key) {
    notify[key] = !notify[key];
    notify.put('/account/notifications', { preserveScroll: true });
}

function savePassword() {
    password.put('/account/password', {
        preserveScroll: true,
        onSuccess: () => {
            password.reset();
            panel.value = null;
        },
    });
}

function destroy() {
    if (!confirm('Видалити акаунт назавжди?')) return;
    del.delete('/account', { preserveScroll: true });
}
</script>
