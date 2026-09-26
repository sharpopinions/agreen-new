<template>
    <footer class="footer">
        <div class="footer__inner container">
            <div class="footer__brand">
                <Link href="/" class="footer__logo">A-green</Link>
                <span class="footer__copy">A-green ©</span>
            </div>

            <nav class="footer__cols">
                <ul v-for="(col, i) in cols" :key="i" class="footer__col">
                    <li v-for="link in col" :key="link.label">
                        <Link :href="link.href" class="footer__link">{{ link.label }}</Link>
                    </li>
                </ul>
            </nav>

            <div class="footer__contacts">
                <div class="footer__contact">
                    <span class="footer__label">Технічна підтримка:</span>
                    <a href="tel:+380673826184" class="footer__value">(067) 382-61-84</a>
                    <a href="tel:+380970757170" class="footer__value">(097) 075-71-70</a>
                </div>
                <div class="footer__contact">
                    <span class="footer__label">Адреса:</span>
                    <span class="footer__value">02660, м. Київ,<br>вул. Крайня 1</span>
                </div>
                <div class="footer__contact">
                    <span class="footer__label">E-mail:</span>
                    <a href="mailto:info@a-green.com.ua" class="footer__value">info@a-green.com.ua</a>
                    <a href="mailto:office@a-green.com.ua" class="footer__value">office@a-green.com.ua</a>
                </div>
                <div class="footer__social">
                    <a href="#" class="footer__social-link" aria-label="Instagram"><Icon name="instagram" :size="24" /></a>
                    <a href="#" class="footer__social-link" aria-label="Facebook"><Icon name="facebook" :size="24" /></a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

const cols = [
    [
        { label: 'Каталог', href: '/catalog' },
        { label: 'Бренди',  href: '/brands'  },
        { label: 'Акції',   href: '/sales'   },
    ],
    [
        { label: 'Навчальний центр',   href: '/services/study-center' },
        { label: 'Технічна підтримка', href: '/services/support'      },
        { label: 'Проектування',       href: '/services/design'       },
    ],
    [
        { label: 'Про компанію', href: '/about'    },
        { label: 'Послуги',      href: '/services' },
    ],
    [
        { label: 'Доставка та оплата',        href: '/delivery'     },
        { label: 'Політика конфіденційності', href: '/privacy'      },
        { label: 'Договір публічної оферти',  href: '/public-offer' },
        { label: 'Повернення та обмін',       href: '/return'       },
    ],
];
</script>
