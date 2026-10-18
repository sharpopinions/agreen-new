import { ref } from 'vue';

// Глобальний стан вікна входу (хедер, кошик, вішліст відкривають його з будь-якої сторінки)
const open = ref(false);
const step = ref('login');

export function useAuthModal() {
    function show(initial = 'login') {
        step.value = initial;
        open.value = true;
    }

    function close() {
        open.value = false;
    }

    return { open, step, show, close };
}
