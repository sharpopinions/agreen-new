<template>
    <footer class="footer">
        <div class="footer__main">
            <div class="footer__brand">
                <div class="footer__logo">
                    <div class="footer__logo-mark">AG</div>
                    <span class="footer__logo-name">A-green</span>
                </div>
                <p class="footer__description">
                    Широкий асортимент продукції для промислових підприємств та кузовного ремонту.
                </p>
                <span class="footer__email">info@a-green.com.ua</span>
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
            <span>© 2026 A-green. Всі права захищені.</span>
            <span>02660, м. Київ, вул. Крайня 1</span>
        </div>
    </footer>
</template>

<script setup>
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
