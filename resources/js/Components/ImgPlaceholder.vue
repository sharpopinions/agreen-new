<template>
    <div class="img-ph" :style="{ height: typeof h === 'number' ? `${h}px` : h, background: bg }">
        <div class="img-ph__dots" :style="{ backgroundImage: dots }"></div>
        <svg class="img-ph__shapes" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <ellipse :cx="shape.cx" :cy="shape.cy" :rx="shape.rx" :ry="shape.ry" :fill="shape.accent" :opacity="dark ? 0.5 : 0.55" />
            <circle :cx="shape.ccx" :cy="shape.ccy" :r="shape.r" :fill="shape.second" opacity="0.6" />
        </svg>
        <div class="img-ph__glow" :class="{ 'img-ph__glow--dark': dark }"></div>
        <span v-if="label" class="img-ph__label">{{ label }}</span>
    </div>
</template>

<script setup>
// Плейсхолдер зображення з Claude Design (components.jsx → Img):
// детермінований м'який градієнт за хешем підпису. Замінити на реальні фото.
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    h:     { type: [Number, String], default: 200 },
    label: { type: String, default: 'фото' },
    seed:  { type: String, default: null },
});

const dark = ref(false);
let observer;
onMounted(() => {
    const read = () => { dark.value = document.documentElement.getAttribute('data-theme') === 'dark'; };
    read();
    observer = new MutationObserver(read);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
});
onBeforeUnmount(() => observer?.disconnect());

const hash = computed(() => {
    const s = props.seed ?? props.label ?? '';
    let h = 0;
    for (let i = 0; i < s.length; i++) h = ((h << 5) - h + s.charCodeAt(i)) | 0;
    return h;
});

const hue  = computed(() => Math.abs(hash.value) % 360);
const hue2 = computed(() => (hue.value + 40) % 360);

const bg = computed(() => {
    const sat  = dark.value ? '18%' : '28%';
    const lig1 = dark.value ? '14%' : '93%';
    const lig2 = dark.value ? '10%' : '88%';
    return `linear-gradient(135deg, hsl(${hue.value}, ${sat}, ${lig1}) 0%, hsl(${hue2.value}, ${sat}, ${lig2}) 100%)`;
});

const dots = computed(() => {
    const c = dark.value ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.04)';
    return `radial-gradient(circle at 1.5px 1.5px, ${c} 1.5px, transparent 0)`;
});

const shape = computed(() => {
    const a = Math.abs(hash.value);
    return {
        cx: 50 + (a % 80),
        cy: 50 + (Math.abs(hash.value >> 3) % 80),
        rx: 40 + (Math.abs(hash.value >> 5) % 30),
        ry: 30 + (Math.abs(hash.value >> 7) % 30),
        ccx: 140 + (Math.abs(hash.value >> 9) % 30),
        ccy: 130 + (Math.abs(hash.value >> 11) % 20),
        r: 20 + (Math.abs(hash.value >> 13) % 15),
        accent: dark.value ? `hsl(${hue.value}, 20%, 22%)` : `hsl(${hue.value}, 35%, 75%)`,
        second: dark.value ? `hsl(${hue2.value}, 25%, 32%)` : `hsl(${hue2.value}, 40%, 80%)`,
    };
});
</script>
