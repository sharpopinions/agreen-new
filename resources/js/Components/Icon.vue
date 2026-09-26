<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        :stroke-width="strokeWidth"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        v-html="icons[name]"
    />
</template>

<script setup>
defineProps({
    name:        { type: String, required: true },
    size:        { type: [Number, String], default: 20 },
    strokeWidth: { type: [Number, String], default: 1.5 },
});

// Лінійні іконки у стилі макета (тонкий контур)
const icons = {
    search:        '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
    user:          '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
    compare:       '<path d="M12 3v18M7 21h10M5 7h14M5 7l-3 7a3 3 0 0 0 6 0L5 7zm14 0-3 7a3 3 0 0 0 6 0l-3-7z"/>',
    heart:         '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
    cart:          '<path d="M3 3h2l2.4 12h11.2L21 7H6.2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>',
    'chevron-down':  '<path d="m6 9 6 6 6-6"/>',
    'chevron-up':    '<path d="m6 15 6-6 6 6"/>',
    'chevron-right': '<path d="m9 6 6 6-6 6"/>',
    'chevron-left':  '<path d="m15 6-6 6 6 6"/>',
    minus:         '<circle cx="12" cy="12" r="10"/><path d="M8 12h8"/>',
    plus:          '<circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>',
    star:          '<path d="m12 2.5 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5-4.8-4.6 6.6-.9z"/>',
    warning:       '<path d="M12 3 2 21h20L12 3z"/><path d="M12 10v5M12 18h.01"/>',
    play:          '<path d="M8 5v14l11-7z"/>',
    instagram:     '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5"/>',
    facebook:      '<circle cx="12" cy="12" r="10"/><path d="M15 8h-2a2 2 0 0 0-2 2v12M9 13h5"/>',
    image:         '<rect x="3" y="3" width="18" height="18"/><path d="m3 3 18 18M21 3 3 21"/>',
};
</script>
