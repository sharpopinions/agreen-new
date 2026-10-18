import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

// Вішліст і порівняння: id товарів приходять зі спільних props (lists), зміни — запитом на сервер
const pending = ref(null);

export function useLists() {
    const page = usePage();
    const wishlist = computed(() => page.props.lists?.wishlist ?? []);
    const compare = computed(() => page.props.lists?.compare ?? []);

    const toggle = (list, productId) => router.post(`/${list}/${productId}`, {}, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => { pending.value = `${list}:${productId}`; },
        onFinish: () => { pending.value = null; },
    });

    return {
        wishlist,
        compare,
        pending,
        inWishlist: id => wishlist.value.includes(id),
        inCompare: id => compare.value.includes(id),
        toggleWishlist: id => toggle('wishlist', id),
        toggleCompare: id => toggle('compare', id),
    };
}
