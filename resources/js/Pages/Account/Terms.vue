<template>
    <AccountLayout title="Умови співпраці">
        <div class="cashback-card">
            <div class="cashback-card__top">
                <div>
                    <div class="cashback-card__label">Кешбек-баланс</div>
                    <div class="cashback-card__balance">{{ fmt(cashback.balance) }}</div>
                    <div class="cashback-card__hint">Поточний кешбек {{ cashback.level?.percent ?? 0 }}% від суми замовлень</div>
                </div>
                <div class="cashback-card__level">
                    <div class="cashback-card__label">Рівень</div>
                    <div class="cashback-card__level-name">{{ cashback.level?.name ?? '—' }}</div>
                </div>
            </div>
            <div v-if="cashback.next" class="cashback-card__progress">
                <div class="cashback-card__progress-row">
                    <span>До рівня «{{ cashback.next.name }}» (кешбек {{ cashback.next.percent }}%)</span>
                    <span>{{ num(cashback.turnover) }} / {{ fmt(cashback.next.from) }}</span>
                </div>
                <div class="cashback-card__bar"><div class="cashback-card__fill" :style="{ width: cashback.progress + '%' }"></div></div>
            </div>
            <div v-else class="cashback-card__hint">У вас найвищий рівень 🎉</div>
        </div>

        <div class="cab-levels">
            <div v-for="l in levels" :key="l.name" class="cab-levels__item" :class="{ 'cab-levels__item--active': l.name === cashback.level?.name }">
                <div class="cab-levels__name">{{ l.name }}</div>
                <div class="cab-levels__percent">{{ l.percent }}%</div>
                <div class="cab-levels__from">{{ l.from > 0 ? `від ${fmt(l.from)}` : 'з першого замовлення' }}</div>
            </div>
        </div>

        <h2 class="cabinet__h3">Історія нарахувань</h2>
        <div v-if="history.length" class="cab-list">
            <div v-for="r in history" :key="r.id" class="cab-list__row cab-tx">
                <span class="cab-tx__amount" :class="r.amount < 0 ? 'cab-tx__amount--minus' : 'cab-tx__amount--plus'">
                    {{ r.amount < 0 ? '−' : '+' }} {{ fmt(Math.abs(r.amount)) }}
                </span>
                <span class="cab-tx__text">{{ r.text }}</span>
                <span class="cab-tx__date">{{ r.date }}</span>
            </div>
        </div>
        <p v-else class="cab-note">Кешбек нараховується автоматично, коли замовлення отримує статус «Виконано».</p>
    </AccountLayout>
</template>

<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { formatPrice as fmt } from '@/utils/format';

defineProps({
    cashback: { type: Object, required: true },
    levels:   { type: Array, default: () => [] },
    history:  { type: Array, default: () => [] },
});

const num = n => Number(n ?? 0).toLocaleString('uk-UA');
</script>
