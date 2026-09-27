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
                        <a
                            v-for="link in col.links"
                            :key="link"
                            href="#"
                            class="footer__link"
                        >{{ link }}</a>
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
    import { useSite } from '@/composables/useSite';

    const site = useSite();
    const year = new Date().getFullYear();
    const socialLabels = {
        instagram: 'Instagram', facebook: 'Facebook', telegram: 'Telegram', viber: 'Viber',
        youtube: 'YouTube', tiktok: 'TikTok', linkedin: 'LinkedIn',
    };

    const cols = [
        {
            title: 'Каталог',
            links: ['Витратні матеріали', 'Абразивні матеріали', 'Дозуюче обладнання', 'Лакофарбові матеріали'],
        },
        {
            title: 'Послуги',
            links: ['Навчальний центр', 'Технічна підтримка', 'Проектування', 'Обслуговування'],
        },
        {
            title: 'Компанія',
            links: ['Про нас', 'Партнерство', 'Бренди', 'Блог та новини', 'Вакансії'],
        },
        {
            title: 'Клієнтам',
            links: ['Доставка та оплата', 'Повернення та обмін', 'Публічна оферта', 'Конфіденційність'],
        },
    ];
</script>
