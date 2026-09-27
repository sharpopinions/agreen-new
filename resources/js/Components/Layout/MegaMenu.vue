<template>
    <div class="mega">
        <!-- Каталог -->
        <template v-if="kind === 'catalog'">
            <div>
                <div class="mega__label">Категорії товарів</div>
                <div class="mega__grid mega__grid--3">
                    <Link v-for="cat in nav.categories" :key="cat.id" :href="`/catalog/${cat.slug}`" class="mega__item">
                        <span class="mega__icon">{{ cat.name.charAt(0) }}</span>
                        <span class="mega__text">
                            <span class="mega__title">{{ cat.name }}</span>
                            <span class="mega__desc">{{ pluralUa(cat.count ?? 0, 'товар', 'товари', 'товарів') }}</span>
                        </span>
                    </Link>
                </div>
                <div class="mega__footer">
                    <Link href="/catalog" class="mega__all">Весь каталог <ArrowIcon /></Link>
                </div>
            </div>
            <Link href="/catalog?sale=1" class="mega__promo mega__promo--link">
                <div>
                    <span class="mega__pill">Акції</span>
                    <div class="mega__promo-title mega__promo-title--lg">Знижки до −20%</div>
                    <div class="mega__promo-desc">Спеціальні пропозиції на бренди Tork, Mirka, 3M та інші</div>
                </div>
                <span class="btn btn--outline btn--sm">Дивитись акції →</span>
            </Link>
        </template>

        <!-- Бренди -->
        <template v-else-if="kind === 'brands'">
            <div>
                <div class="mega__label">Популярні бренди</div>
                <div class="mega__grid mega__grid--5">
                    <Link v-for="brand in nav.brands" :key="brand.id" :href="`/catalog?brand[]=${brand.id}`" class="mega__brand">
                        <span class="mega__brand-icon">{{ brand.name.charAt(0) }}</span>
                        <span class="mega__brand-name">{{ brand.name }}</span>
                    </Link>
                </div>
                <div class="mega__footer">
                    <Link href="/brands" class="mega__all">Усі бренди <ArrowIcon /></Link>
                </div>
            </div>
            <div class="mega__promo">
                <div>
                    <div class="mega__label">Стати дилером</div>
                    <div class="mega__promo-title">Партнерська програма</div>
                    <div class="mega__promo-desc">Знижки до 25%, дропшипінг, маркетингова підтримка</div>
                </div>
                <Link href="/partners" class="btn btn--primary btn--sm">Дізнатись →</Link>
            </div>
        </template>

        <!-- Послуги -->
        <template v-else-if="kind === 'services'">
            <div>
                <div class="mega__label">Що ми робимо</div>
                <div class="mega__grid mega__grid--2">
                    <Link v-for="s in services" :key="s.title" href="/services" class="mega__item">
                        <span class="mega__icon mega__icon--emoji">{{ s.icon }}</span>
                        <span class="mega__text">
                            <span class="mega__title">{{ s.title }}</span>
                            <span class="mega__desc">{{ s.desc }}</span>
                        </span>
                    </Link>
                </div>
            </div>
            <div class="mega__promo">
                <div>
                    <div class="mega__promo-title">Замовити сервіс</div>
                    <div class="mega__promo-desc">Залиште заявку — наш менеджер зв'яжеться протягом 1 робочого дня</div>
                </div>
                <Link href="/contacts" class="btn btn--primary btn--sm">Залишити заявку →</Link>
            </div>
        </template>

        <!-- Про нас -->
        <template v-else-if="kind === 'about'">
            <div>
                <div class="mega__label">Компанія</div>
                <div class="mega__grid mega__grid--2">
                    <Link v-for="l in aboutLinks" :key="l.href" :href="l.href" class="mega__item mega__item--plain">
                        <span class="mega__text">
                            <span class="mega__title">{{ l.title }}</span>
                            <span class="mega__desc">{{ l.desc }}</span>
                        </span>
                    </Link>
                </div>
            </div>
            <div class="mega__promo mega__promo--contacts">
                <div class="mega__label">Контакти</div>
                <div class="mega__contact">
                    <span class="mega__contact-label">Телефон</span>
                    <a href="tel:+380670757170" class="mega__contact-value">+380 67 075-71-70</a>
                </div>
                <div class="mega__contact">
                    <span class="mega__contact-label">Email</span>
                    <a href="mailto:info@a-green.ua" class="mega__contact-value">info@a-green.ua</a>
                </div>
                <div class="mega__contact">
                    <span class="mega__contact-label">Графік</span>
                    <span class="mega__contact-value">Пн–Пт · 9:00–18:00</span>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { h } from 'vue';
import { Link } from '@inertiajs/vue3';
import { pluralUa } from '@/utils/format';

defineProps({
    kind: { type: String, required: true },
    nav:  { type: Object, default: () => ({ categories: [], brands: [] }) },
});

// Контент із Claude Design (mega-menus.jsx)
const services = [
    { icon: '🎓', title: 'Навчальний центр',     desc: 'Курси та сертифікація' },
    { icon: '🛠️', title: 'Сервісні роботи',       desc: 'Ремонт обладнання' },
    { icon: '🚚', title: 'Доставка',              desc: 'По Україні та Європі' },
    { icon: '📋', title: 'Технічна консультація', desc: 'Підбір рішень' },
    { icon: '🎨', title: 'Підбір кольору',        desc: 'Лабораторія A-green' },
    { icon: '📦', title: 'Дропшипінг',            desc: 'Для дилерів' },
];

const aboutLinks = [
    { title: 'Про компанію', desc: 'Історія, місія, цінності', href: '/about'     },
    { title: 'Бренди',       desc: 'Наші партнери',            href: '/brands'    },
    { title: 'Партнерство',  desc: 'Стати дилером A-green',    href: '/partners'  },
    { title: 'Доставка',     desc: 'Умови та терміни',         href: '/delivery'  },
    { title: 'Контакти',     desc: "Адреси та зв'язок",        href: '/contacts'  },
    { title: 'Вакансії',     desc: 'Приєднайся до команди',    href: '/vacancies' },
];

const ArrowIcon = () => h('svg', { width: 12, height: 12, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2 },
    [h('path', { d: 'M5 12h14M13 5l7 7-7 7' })]);
</script>
