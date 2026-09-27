import { ref } from 'vue';

// Глобальний стан палітри Cmd+K: відкривається з хедера або гарячою клавішею
const open = ref(false);

export function useCommandPalette() {
    return {
        open,
        openPalette:  () => { open.value = true; },
        closePalette: () => { open.value = false; },
    };
}
