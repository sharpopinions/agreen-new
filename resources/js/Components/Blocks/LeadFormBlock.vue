<template>
    <!-- Блок «Форма заявки»: картки вибору (типи партнерства, послуги) + переваги + форма -->
    <section :id="`form-${block.form}`" class="block-lead">
        <template v-if="block.options.length">
            <h2 v-if="block.optionsHeading" class="block-heading">{{ block.optionsHeading }}</h2>
            <div class="block-lead__options">
                <button
                    v-for="o in block.options"
                    :key="o.label"
                    type="button"
                    class="block-lead__option"
                    :class="{ 'block-lead__option--active': choice === o.label }"
                    @click="pick(o.label)"
                >
                    <span class="block-lead__option-title">{{ o.label }}</span>
                    <span v-if="o.text" class="block-lead__option-text">{{ o.text }}</span>
                    <span class="btn btn--outline btn--sm">Подати заявку →</span>
                </button>
            </div>
        </template>

        <div ref="formBox" class="block-lead__box">
            <div>
                <h2 class="block-heading">{{ block.heading || defaults.heading }}</h2>
                <template v-if="block.perks.length">
                    <div v-if="block.perksHeading" class="block-lead__perks-title">{{ block.perksHeading }}</div>
                    <ul class="check-list">
                        <li v-for="p in block.perks" :key="p" class="check-list__item">{{ p }}</li>
                    </ul>
                </template>
            </div>

            <div>
                <LeadSuccess v-if="sent" :title="defaults.success" text="Ми зв'яжемось з вами протягом одного робочого дня." again @again="again" />
                <form v-else class="lead-form" novalidate @submit.prevent="send">
                    <div v-if="block.options.length" class="segmented" role="radiogroup" :aria-label="block.optionsHeading || 'Тип'">
                        <button
                            v-for="o in block.options"
                            :key="o.label"
                            type="button"
                            class="segmented__item"
                            :class="{ 'segmented__item--active': choice === o.label }"
                            role="radio"
                            :aria-checked="choice === o.label"
                            @click="choice = o.label"
                        >{{ o.label }}</button>
                    </div>
                    <Field v-if="block.form === 'partner'" v-model="form.company" label="Назва компанії" required placeholder="ТОВ «Назва компанії»" autocomplete="organization" :error="form.errors.company" />
                    <Field v-model="form.name" :label="block.form === 'partner' ? 'Контактна особа' : 'Ім\'я'" required placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
                    <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                    <Field v-model="form.email" label="Email" :required="block.form === 'partner'" type="email" placeholder="email@company.ua" autocomplete="email" :error="form.errors.email" />
                    <Field v-if="block.form === 'service'" v-model="form.company" label="Компанія" placeholder="Назва компанії" autocomplete="organization" :error="form.errors.company" />
                    <Field v-if="block.form !== 'partner'" v-model="form.message" label="Коментар" textarea placeholder="Ваше запитання..." :error="form.errors.message" />
                    <Honeypot v-model="form.website" />
                    <button class="btn btn--primary" type="submit" :disabled="form.processing">{{ defaults.submit }}</button>
                    <p class="blog-event__legal">Надсилаючи форму, ви погоджуєтесь з <Link href="/privacy">політикою конфіденційності</Link></p>
                </form>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Field from '@/Components/Form/Field.vue';
import Honeypot from '@/Components/Form/Honeypot.vue';
import LeadSuccess from '@/Components/Form/LeadSuccess.vue';
import { useLeadForm } from '@/composables/useLeadForm';

const props = defineProps({
    block: { type: Object, required: true },
});

const DEFAULTS = {
    partner: { heading: 'Заявка на партнерство', submit: 'Стати партнером', success: 'Заявку надіслано!' },
    service: { heading: 'Замовити послугу', submit: 'Надіслати заявку', success: 'Заявку прийнято!' },
    contact: { heading: 'Написати нам', submit: 'Надіслати', success: 'Повідомлення надіслано!' },
};
const defaults = computed(() => DEFAULTS[props.block.form] ?? DEFAULTS.contact);

const { form, sent, submit, again } = useLeadForm(props.block.form, { company: '', name: '', phone: '', email: '', message: '' });
const choice  = ref(props.block.options[0]?.label ?? null);
const formBox = ref(null);

function pick(label) {
    choice.value = label;
    formBox.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function send() {
    const key = props.block.form === 'partner' ? 'partner_type' : 'service';
    submit(choice.value ? { [key]: choice.value } : {});
}
</script>
