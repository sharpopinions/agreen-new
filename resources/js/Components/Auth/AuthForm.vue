<template>
    <!-- Claude Design → auth-modal.jsx: вхід / реєстрація / код / вибір ролі -->
    <div class="auth" :class="{ 'auth--split': split, 'auth--wide': step === 'role' }">
        <div class="auth__main">
            <!-- ВХІД -->
            <form v-if="step === 'login'" class="auth__form" novalidate @submit.prevent="submitLogin">
                <h2 class="auth__title">Вхід до особистого кабінету</h2>
                <p v-if="notice" class="auth__notice">{{ notice }}</p>
                <Field v-model="login.login" label="Електронна пошта або телефон +380" placeholder="email@example.com або +380..." autocomplete="username" :error="login.errors.login" />
                <Field v-model="login.password" label="Пароль" type="password" placeholder="••••••••" autocomplete="current-password" :error="login.errors.password" />
                <div class="auth__row auth__row--end">
                    <button type="button" class="auth__link" @click="go('forgot')">Забули пароль?</button>
                </div>
                <div class="auth__row">
                    <button class="btn btn--primary" type="submit" :disabled="login.processing">Увійти</button>
                    <label class="auth__check"><input v-model="login.remember" type="checkbox" /> Запам'ятати мене</label>
                </div>
                <p class="auth__switch">
                    Немає профілю? <button type="button" class="auth__link auth__link--strong" @click="go('register')">ЗАРЕЄСТРУВАТИСЬ</button>
                </p>
            </form>

            <!-- РЕЄСТРАЦІЯ -->
            <form v-else-if="step === 'register'" class="auth__form" novalidate @submit.prevent="submitRegister">
                <h2 class="auth__title">Створити профіль</h2>
                <div class="auth__grid">
                    <Field v-model="register.name" label="Ім'я" required placeholder="Іван" autocomplete="given-name" :error="register.errors.name" />
                    <Field v-model="register.last_name" label="Прізвище" required placeholder="Іваненко" autocomplete="family-name" :error="register.errors.last_name" />
                </div>
                <Field v-model="register.phone" label="Номер телефону" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="register.errors.phone" />
                <Field v-model="register.email" label="Електронна пошта" required type="email" placeholder="email@example.com" autocomplete="email" :error="register.errors.email" />
                <div class="auth__grid">
                    <Field v-model="register.password" label="Пароль" required type="password" placeholder="••••••••" autocomplete="new-password" :error="register.errors.password" />
                    <Field v-model="register.password_confirmation" label="Повторити пароль" required type="password" placeholder="••••••••" autocomplete="new-password" />
                </div>
                <label class="auth__check">
                    <input v-model="register.policy" type="checkbox" />
                    <span>Погоджуюсь з <Link href="/privacy" target="_blank">Політикою конфіденційності</Link></span>
                </label>
                <p v-if="register.errors.policy" class="field__error">{{ register.errors.policy }}</p>
                <button class="btn btn--primary btn--full" type="submit" :disabled="!register.policy || register.processing">Зареєструватись</button>
                <p class="auth__switch">
                    <button type="button" class="auth__link" @click="go('login')">Увійти</button>
                    <span> | </span>
                    <button type="button" class="auth__link" @click="go('forgot')">Забули пароль?</button>
                </p>
            </form>

            <!-- ПІДТВЕРДЖЕННЯ EMAIL -->
            <form v-else-if="step === 'confirm'" class="auth__form auth__form--center" novalidate @submit.prevent="submitCode">
                <div class="auth__icon auth__icon--blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <h2 class="auth__title">Підтвердіть реєстрацію</h2>
                <p class="auth__text">Ми надіслали лист з кодом підтвердження на адресу</p>
                <p class="auth__email">{{ email }}</p>
                <div class="auth__code" @paste.prevent="pasteCode">
                    <input
                        v-for="(c, i) in digits"
                        :key="i"
                        :ref="el => (codeInputs[i] = el)"
                        v-model="digits[i]"
                        class="auth__digit"
                        :class="{ 'auth__digit--filled': c }"
                        inputmode="numeric"
                        maxlength="1"
                        :aria-label="`Цифра ${i + 1}`"
                        @input="onDigit(i)"
                        @keydown.backspace="onBackspace(i)"
                    />
                </div>
                <p v-if="code.errors.code" class="field__error">{{ code.errors.code }}</p>
                <button class="btn btn--primary btn--full" type="submit" :disabled="digits.some(d => !d) || code.processing">Активувати акаунт</button>
                <p class="auth__hint">
                    <template v-if="timer > 0">Не прийшло? Надіслати ще раз через <strong>{{ timer }} сек</strong></template>
                    <button v-else type="button" class="auth__link" @click="resend">Надіслати ще раз</button>
                </p>
            </form>

            <!-- ВИБІР РОЛІ -->
            <form v-else-if="step === 'role'" class="auth__form" novalidate @submit.prevent="submitRole">
                <div class="auth__form--center">
                    <div class="auth__icon auth__icon--green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h2 class="auth__title">Дякуємо, ваш акаунт активовано!</h2>
                    <p class="auth__text">Оберіть, як ви плануєте користуватися платформою</p>
                </div>
                <div class="role-grid" role="radiogroup">
                    <button
                        v-for="r in roles"
                        :key="r.key"
                        type="button"
                        class="role-card"
                        :class="{ 'role-card--active': roleForm.role === r.key }"
                        role="radio"
                        :aria-checked="roleForm.role === r.key"
                        @click="roleForm.role = r.key"
                    >
                        <span class="role-card__head">
                            <span class="role-card__title">{{ r.title }}</span>
                            <span class="role-card__radio"></span>
                        </span>
                        <span v-for="f in r.feats" :key="f" class="role-card__feat">{{ f }}</span>
                    </button>
                </div>
                <div v-if="isBusiness" class="auth__grid">
                    <Field v-model="roleForm.company" label="Назва компанії" required placeholder="ТОВ «Назва»" autocomplete="organization" :error="roleForm.errors.company" />
                    <Field v-model="roleForm.company_code" label="ЄДРПОУ / ІПН" placeholder="12345678" :error="roleForm.errors.company_code" />
                </div>
                <p v-if="isBusiness" class="auth__text auth__text--left">Менеджер перевірить дані й підтвердить бізнес-умови. До того кабінет працює як для звичайного клієнта.</p>
                <div class="auth__row auth__row--between">
                    <span></span>
                    <button class="btn btn--primary btn--lg" type="submit" :disabled="roleForm.processing">{{ roleForm.role === 'client' ? 'Перейти до кабінету' : 'Подати заявку' }}</button>
                </div>
            </form>

            <!-- ЗАБУЛИ ПАРОЛЬ -->
            <form v-else-if="step === 'forgot'" class="auth__form" novalidate @submit.prevent="submitForgot">
                <h2 class="auth__title">Відновлення пароля</h2>
                <p class="auth__text auth__text--left">Вкажіть email — надішлемо посилання для встановлення нового пароля.</p>
                <Field v-model="forgot.email" label="Електронна пошта" type="email" placeholder="email@example.com" autocomplete="email" :error="forgot.errors.email" />
                <button class="btn btn--primary btn--full" type="submit" :disabled="forgot.processing">Надіслати посилання</button>
                <p class="auth__switch"><button type="button" class="auth__link" @click="go('login')">← Повернутись до входу</button></p>
            </form>

            <div v-else-if="step === 'forgot_sent'" class="auth__form auth__form--center">
                <div class="auth__icon auth__icon--blue">✉</div>
                <h2 class="auth__title">Перевірте пошту</h2>
                <p class="auth__text">Якщо такий email зареєстровано, ми надіслали посилання для зміни пароля. Воно діє 60 хвилин.</p>
                <button type="button" class="btn btn--outline" @click="go('login')">До входу</button>
            </div>
        </div>

        <!-- Переваги (лише вхід / реєстрація) -->
        <aside v-if="split" class="auth__aside">
            <div class="auth__logo">AG</div>
            <div class="auth__aside-title">{{ step === 'login' ? 'Раді бачити вас знову' : 'Переваги реєстрації' }}</div>
            <p class="auth__aside-text">
                {{ step === 'login'
                    ? 'Увійдіть, щоб керувати замовленнями, відстежувати доставку та користуватись бонусами.'
                    : 'Створіть профіль та отримайте доступ до спеціальних умов для постійних клієнтів.' }}
            </p>
            <ul class="auth__benefits">
                <li v-for="b in benefits[step]" :key="b">{{ b }}</li>
            </ul>
        </aside>
    </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Field from '@/Components/Form/Field.vue';

const props = defineProps({
    step: { type: String, default: 'login' },
});
const emit = defineEmits(['update:step', 'done']);

const page = usePage();
const step = computed(() => props.step);
const split = computed(() => ['login', 'register'].includes(step.value));
const go = (s) => emit('update:step', s);

const notice = computed(() => page.props.flash?.auth?.step === 'reset_done' ? 'Пароль змінено — увійдіть з новим паролем.' : null);

const benefits = {
    login: ['Перегляд історії замовлень', 'Бонуси та персональні знижки', 'Швидке повторне замовлення', 'Особистий менеджер'],
    register: ['Лояльні ціни для постійних клієнтів', 'Накопичувальна система кешбеку', 'Доступ до закритих акцій', 'Документообіг онлайн'],
};

const roles = [
    { key: 'client', title: 'Стати клієнтом', feats: ['Без заключення договору', 'Перегляд історії замовлень', 'Підключення до системи бонусів та знижок'] },
    { key: 'business_client', title: 'Стати бізнес-клієнтом', feats: ['Заключення договору', 'Закупівля продукції гуртом', 'Дистриб’юторські умови', 'Персональний менеджер', 'Навчання та техпідтримка'] },
    { key: 'business_partner', title: 'Стати бізнес-партнером', feats: ['Заключення договору', 'Гуртові закупівлі', 'Дропшипінг', 'Персональний менеджер', 'Навчання та техпідтримка'] },
    { key: 'undecided', title: 'Я ще не визначився', feats: ['Замовити консультацію', 'Менеджер допоможе обрати', 'Без зобов’язань'] },
];

const opts = { preserveScroll: true, preserveState: true };

const login = useForm({ login: '', password: '', remember: false });
const register = useForm({ name: '', last_name: '', phone: '', email: '', password: '', password_confirmation: '', policy: false });
const code = useForm({ code: '' });
const roleForm = useForm({ role: 'client', company: '', company_code: '' });
const forgot = useForm({ email: '' });

const isBusiness = computed(() => ['business_client', 'business_partner'].includes(roleForm.role));
const email = ref(page.props.auth?.user?.email ?? '');

// Сервер повідомляє наступний крок через flash.auth
watch(() => page.props.flash?.auth, (auth) => {
    if (!auth?.step) return;
    if (auth.email) email.value = auth.email;
    if (['confirm', 'role', 'forgot_sent'].includes(auth.step)) go(auth.step);
    if (auth.step === 'done') emit('done');
}, { immediate: true });

function submitLogin() { login.post('/auth/login', { ...opts, onFinish: () => login.reset('password') }); }
function submitRegister() { register.post('/auth/register', { ...opts, onFinish: () => register.reset('password', 'password_confirmation') }); }
function submitForgot() { forgot.post('/auth/forgot', opts); }
function submitRole() { roleForm.post('/auth/role', { ...opts, preserveState: false, onSuccess: () => emit('done') }); }

// Код з 6 цифр
const digits = ref(['', '', '', '', '', '']);
const codeInputs = [];

function onDigit(i) {
    digits.value[i] = digits.value[i].replace(/\D/g, '').slice(-1);
    if (digits.value[i] && i < 5) codeInputs[i + 1]?.focus();
}
function onBackspace(i) {
    if (!digits.value[i] && i > 0) codeInputs[i - 1]?.focus();
}
function pasteCode(e) {
    const text = (e.clipboardData?.getData('text') ?? '').replace(/\D/g, '').slice(0, 6);
    text.split('').forEach((d, i) => { digits.value[i] = d; });
    codeInputs[Math.min(text.length, 5)]?.focus();
}
function submitCode() {
    code.code = digits.value.join('');
    code.post('/auth/verify', opts);
}

const timer = ref(60);
let interval = null;
function startTimer() {
    timer.value = 60;
    clearInterval(interval);
    interval = setInterval(() => { timer.value = Math.max(0, timer.value - 1); }, 1000);
}
function resend() {
    router.post('/auth/resend', {}, { ...opts, onSuccess: startTimer });
}

watch(step, async (s) => {
    if (s === 'confirm') {
        startTimer();
        await nextTick();
        codeInputs[0]?.focus();
    }
}, { immediate: true });

onBeforeUnmount(() => clearInterval(interval));
</script>
