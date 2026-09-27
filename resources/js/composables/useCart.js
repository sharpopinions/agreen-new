import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

// Бічна панель кошика — спільний стан для хедера й сторінок
const drawerOpen = ref(false);
const pending = ref(false);

export function useCart() {
    const page = usePage();
    const cart = computed(() => page.props.cart ?? { items: [], count: 0, total: 0, regularTotal: 0, preorderCount: 0 });

    const opts = (extra = {}) => ({
        preserveScroll: true,
        preserveState: true,
        onStart: () => { pending.value = true; },
        onFinish: () => { pending.value = false; },
        ...extra,
    });

    return {
        cart,
        pending,
        drawerOpen,
        add: (productId, quantity = 1) => router.post('/cart', { product_id: productId, quantity }, opts()),
        setQty: (productId, quantity) => router.patch(`/cart/${productId}`, { quantity }, opts()),
        remove: (productId) => router.delete(`/cart/${productId}`, opts()),
        clear: () => router.delete('/cart', opts()),
        openDrawer: () => { drawerOpen.value = true; },
        closeDrawer: () => { drawerOpen.value = false; },
    };
}
