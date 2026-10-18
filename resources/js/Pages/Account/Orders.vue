<template>
    <AccountLayout title="Замовлення">
        <div v-if="orders.data.length" class="cab-orders">
            <article
                v-for="o in orders.data"
                :key="o.id"
                class="cab-order"
                :class="{ 'cab-order--open': open === o.id }"
            >
                <button type="button" class="cab-order__head" :aria-expanded="open === o.id" @click="open = open === o.id ? null : o.id">
                    <span class="cab-order__main">
                        <span class="cab-order__number">#{{ o.number }}</span>
                        <span class="cab-order__meta">
                            {{ o.date }} · {{ o.count }} тов.<template v-if="o.type === 'preorder'"> · передзамовлення</template>
                        </span>
                    </span>
                    <span v-if="o.status.name" class="status-pill" :style="{ '--pill': o.status.color || '#71717a' }">{{ o.status.name }}</span>
                    <span class="cab-order__total">{{ fmt(o.total) }}</span>
                    <svg class="cab-order__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" /></svg>
                </button>

                <div v-if="open === o.id" class="cab-order__body">
                    <ul class="cab-order__items">
                        <li v-for="(i, n) in o.items" :key="n" class="cab-order__item">
                            <component :is="i.slug ? Link : 'span'" :href="i.slug ? `/p/${i.slug}` : undefined" class="cab-order__item-name">{{ i.name }}</component>
                            <span class="cab-order__item-sku">{{ i.sku }}</span>
                            <span class="cab-order__item-qty">{{ i.quantity }} × {{ fmt(i.price) }}</span>
                        </li>
                    </ul>
                    <dl v-if="o.delivery || o.payment" class="cab-order__info">
                        <template v-if="o.delivery">
                            <dt>Доставка</dt>
                            <dd>{{ [o.delivery, o.city, o.address].filter(Boolean).join(', ') }}</dd>
                        </template>
                        <template v-if="o.tracking">
                            <dt>ТТН</dt><dd>{{ o.tracking }}</dd>
                        </template>
                        <template v-if="o.payment">
                            <dt>Оплата</dt><dd>{{ o.payment }}</dd>
                        </template>
                    </dl>
                    <div class="cab-order__actions">
                        <button type="button" class="btn btn--primary btn--sm" :disabled="busy" @click="repeat(o)">↻ Повторити замовлення</button>
                        <a v-if="o.invoice" :href="o.invoice" class="btn btn--outline btn--sm">↓ Рахунок-фактура</a>
                    </div>
                </div>
            </article>

            <nav v-if="orders.prev_page_url || orders.next_page_url" class="cab-pager">
                <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="btn btn--outline btn--sm">← Новіші</Link>
                <span class="cab-pager__info">Сторінка {{ orders.current_page }} з {{ orders.last_page }}</span>
                <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="btn btn--outline btn--sm">Старіші →</Link>
            </nav>
        </div>

        <div v-else class="cab-empty">
            <div class="cab-empty__icon">◫</div>
            <p class="cab-empty__title">Замовлень ще немає</p>
            <p class="cab-empty__text">Тут зʼявиться історія ваших замовлень зі статусами й можливістю повторити замовлення.</p>
            <Link href="/catalog" class="btn btn--primary">Перейти до каталогу</Link>
        </div>
    </AccountLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { formatPrice as fmt } from '@/utils/format';

const props = defineProps({
    orders: { type: Object, required: true },
});

const open = ref(props.orders.data[0]?.id ?? null);
const busy = ref(false);

function repeat(order) {
    router.post(`/account/orders/${order.id}/repeat`, {}, {
        onStart: () => (busy.value = true),
        onFinish: () => (busy.value = false),
    });
}
</script>
