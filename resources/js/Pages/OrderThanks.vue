<template>
    <AppLayout>
        <div class="thanks">
            <div class="thanks__icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h1 class="thanks__title">Дякуємо за замовлення!</h1>
            <p class="thanks__text">
                <template v-if="orders.length > 1">Ми створили два замовлення: {{ orders.map(o => '№' + o.number).join(' та ') }}.</template>
                <template v-else>Ваше замовлення №{{ orders[0].number }} успішно оформлене.</template>
            </p>
            <p class="thanks__sub">Менеджер зв'яжеться з вами найближчим часом для підтвердження.</p>

            <div v-for="o in orders" :key="o.number" class="thanks__card">
                <div class="thanks__card-head">
                    <span>{{ o.type === 'preorder' ? 'Передзамовлення' : 'Замовлення' }} №{{ o.number }}</span>
                    <span v-if="o.type === 'regular'" class="thanks__meta">{{ o.delivery }} · {{ o.payment }}</span>
                    <span v-else class="thanks__meta">Без оплати — ціну й строк підтвердить менеджер</span>
                </div>
                <div class="thanks__grid">
                    <span class="thanks__th">Товар</span>
                    <span class="thanks__th thanks__c">Кількість</span>
                    <span class="thanks__th thanks__r">Сума</span>
                    <template v-for="i in o.items" :key="i.sku">
                        <span>{{ i.name }}</span>
                        <span class="thanks__c">{{ i.quantity }}</span>
                        <span class="thanks__r">{{ fmt(i.total) }}</span>
                    </template>
                </div>
                <div class="thanks__total"><span>Разом:</span><span>{{ fmt(o.total) }}</span></div>
            </div>

            <div class="thanks__actions">
                <Link href="/" class="btn btn--primary">На головну</Link>
                <Link href="/catalog" class="btn btn--outline">Продовжити покупки</Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatPrice as fmt } from '@/utils/format';

defineProps({ orders: Array });
</script>
