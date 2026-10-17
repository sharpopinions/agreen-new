<template>
    <footer class="footer">
        <div class="footer__main">
            <div class="footer__brand">
                <div class="footer__logo">
                    <div class="footer__logo-mark">AG</div>
                    <span class="footer__logo-name">A-green</span>
                </div>
                <p class="footer__description">{{ site.footerText }}</p>
                <a v-if="site.emails[0]" :href="`mailto:${site.emails[0]}`" class="footer__email">{{ site.emails[0] }}</a>
                <div v-if="site.socials.length" class="footer__socials">
                    <a
                        v-for="s in site.socials"
                        :key="s.key"
                        :href="s.url"
                        class="footer__social"
                        target="_blank"
                        rel="noopener"
                    >{{ socialLabels[s.key] ?? s.key }}</a>
                </div>
            </div>

            <div class="footer__cols">
                <div v-for="col in cols" :key="col.title" class="footer__col">
                    <div class="footer__col-title">{{ col.title }}</div>
                    <div class="footer__col-links">
                        <Link
                            v-for="link in col.links"
                            :key="link.label"
                            :href="link.href"
                            class="footer__link"
                        >{{ link.label }}</Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <span>© {{ year }} A-green. Всі права захищені.</span>
            <span>{{ site.address }}</span>
        </div>
    </footer>
</template>

<script setup>
    import { computed } from 'vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import { useSite } from '@/composables/useSite';

    const site = useSite();
    const year = new Date().getFullYear();
    const socialLabels = {
        instagram: 'Instagram', facebook: 'Facebook', telegram: 'Telegram', viber: 'Viber',
        youtube: 'YouTube', tiktok: 'TikTok', linkedin: 'LinkedIn',
    };

    // Каталог — перші категорії з меню; решта — сторінки сайту (Claude Design → Footer)
    const page = usePage();
    const cols = computed(() => [
        {
            title: 'Каталог',
            links: (page.props.nav?.categories ?? []).slice(0, 4).map(c => ({ label: c.name, href: `/catalog/${c.slug}` })),
        },
        {
            title: 'Послуги',
            links: (page.props.nav?.services ?? []).slice(0, 4).map(s => ({ label: s.title, href: `/services/${s.slug}` })),
        },
        {
            title: 'Компанія',
            links: [
                { label: 'Про нас',        href: '/about' },
                { label: 'Партнерство',    href: '/partners' },
                { label: 'Бренди',         href: '/brands' },
                { label: 'Блог та новини', href: '/blog' },
                { label: 'Вакансії',       href: '/vacancies' },
            ],
        },
        {
            title: 'Клієнтам',
            links: [
                { label: 'Доставка та оплата', href: '/delivery' },
                { label: 'Повернення та обмін', href: '/returns' },
                { label: 'Публічна оферта',    href: '/public-offer' },
                { label: 'Конфіденційність',   href: '/privacy' },
            ],
        },
    ]);
</script>
