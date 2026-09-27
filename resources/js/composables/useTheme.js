import { ref } from 'vue';

// Спільний стан теми для хедера й палітри Cmd+K
const isDark = ref(document.documentElement.getAttribute('data-theme') === 'dark');

export function useTheme() {
    function toggleTheme() {
        isDark.value = !isDark.value;
        document.documentElement.setAttribute('data-theme', isDark.value ? 'dark' : 'light');
    }

    return { isDark, toggleTheme };
}
