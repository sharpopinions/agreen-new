<template>
      <AppLayout>

          <!-- Hero -->
          <section class="home-hero">
              <div class="home-hero__sidebar">
                  <div class="home-hero__sidebar-title">Категорії</div>
                  <div
                      v-for="cat in categories"
                      :key="cat.id"
                      class="home-hero__sidebar-item"
                  >
                      <span>{{ cat.name }}</span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="m9 18 6-6-6-6"/>
                      </svg>
                  </div>
                  <div class="home-hero__sidebar-all">Весь каталог →</div>
              </div>

              <div class="home-hero__banner">
                  <div class="home-hero__banner-img">Головний банер</div>
                  <div class="home-hero__banner-overlay">
                      <div class="home-hero__banner-label">Промислові товари</div>
                      <h1 class="home-hero__banner-title">
                          Надаємо широкий<br>асортимент продукції
                      </h1>
                      <p class="home-hero__banner-desc">
                          Для комплексного обслуговування підприємств малярно-кузовного ремонту, промислових та виробничих підприємств.
                      </p>
                      <div class="home-hero__banner-btns">
                          <button class="btn btn--primary">Замовити</button>
                          <button class="btn btn--outline">До каталогу</button>
                      </div>
                  </div>
              </div>

              <div class="home-hero__partner">
                  <div class="home-hero__partner-img">Фото партнерства</div>
                  <div class="home-hero__partner-body">
                      <div class="home-hero__partner-title">Партнерство</div>
                      <p class="home-hero__partner-desc">
                          Вигідні умови для дилерів та оптових покупців. Гнучка система знижок від 5%.
                      </p>
                      <button class="btn btn--outline btn--full">Стати партнером →</button>
                  </div>
              </div>
          </section>
  
          <!-- Brands -->
          <section class="home-brands">
              <div class="home-brands__bar">
                  <span class="home-brands__label">Бренди</span>
                  <div class="home-brands__divider"></div>
                  <button
                      v-for="brand in brands"
                      :key="brand"
                      class="home-brands__item"
                  >{{ brand }}</button>
                  <div class="home-brands__all">
                      <button class="btn btn--ghost">Дивитись усі бренди →</button>
                  </div>
              </div>
          </section>
  
          <!-- Categories -->
          <section class="home-categories">
              <div class="section-header">
                  <div class="section-header__text">
                      <h2 class="section-header__title">Популярні категорії</h2>
                      <p class="section-header__subtitle">Понад 5 000 товарів у наявності</p>
                  </div>
                  <button class="btn btn--ghost">Дивитись все →</button>
              </div>
              <div class="home-categories__grid">
                  <CategoryCard
                      v-for="cat in categories.slice(0, 6)"
                      :key="cat.id"
                      :category="cat"
                  />
              </div>
              <div v-if="!showAllCategories" class="home-categories__more">
                <button class="btn btn--outline" @click="showAllCategories = true">
                    Показати ще
                </button>
            </div>
          </section>
  
          <!-- Products -->
          <section class="home-products">
              <div class="section-header">
                  <h2 class="section-header__title">Каталог</h2>
                  <div class="home-products__tabs">
                      <button
                          v-for="tab in tabs"
                          :key="tab.key"
                          class="home-products__tab"
                          :class="{ 'home-products__tab--active': activeTab === tab.key }"
                          @click="activeTab = tab.key"
                      >{{ tab.label }}</button>
                  </div>
              </div>
              <div class="home-products__grid">
                  <ProductCard
                      v-for="product in filteredProducts"
                      :key="product.id"
                      :product="product"
                  />
              </div>
              <div class="home-categories__more">
                  <button class="btn btn--outline">Показати ще</button>
              </div>
          </section>
  
      </AppLayout>
  </template>

  <script setup>
  import { ref, computed } from 'vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import ProductCard from '@/Components/ProductCard.vue';
  import CategoryCard from '@/Components/CategoryCard.vue';
  
    const activeTab = ref('popular');

    const showAllCategories = ref(false);
    const visibleCategories = computed(() =>
        showAllCategories.value ? categories : categories.slice(0, 6)
    );


  const tabs = [
      { key: 'popular', label: 'Популярні'       },
      { key: 'sale',    label: 'Акційні'          },
      { key: 'new',     label: 'Передзамовлення'  },
  ];

  const categories = [
      { id: 1, name: 'Витратні матеріали',      count: 1240, subs: ['Ганчірки та серветки', 'Засоби для прибирання', 'Паперова продукція', 'Мішки та пакети'] },
      { id: 2, name: 'Абразивні матеріали',     count: 876,  subs: ['Шліфувальні диски', 'Абразивні круги', 'Шліфувальна шкурка', 'Полірувальні пасти'] },
      { id: 3, name: 'Дозуюче обладнання',      count: 342,  subs: ['Диспенсери', 'Дозатори рідини', 'Автоматичні системи', 'Аксесуари'] },
      { id: 4, name: 'Лакофарбові матеріали',   count: 2100, subs: ['Ґрунтовки', 'Автолаки', 'Фарби', 'Розчинники'] },
      { id: 5, name: 'Гігієнічна продукція',    count: 560,  subs: ['Мило та антисептики', 'Паперові рушники', 'Туалетний папір', 'Засоби гігієни'] },
      { id: 6, name: 'Захисні засоби',          count: 430,  subs: ['Рукавиці захисні', 'Захисні комбінезони', 'Маски та респіратори', 'Окуляри'] },
      { id: 7, name: 'Клеї та герметики',       count: 280,  subs: ['Конструкційні клеї', 'Силіконові герметики', 'Монтажна піна', 'Двосторонні стрічки'] },
      { id: 8, name: 'Полірувальне обладнання', count: 195,  subs: ['Полірувальні машини', 'Полірувальні круги', 'Паста для полірування', 'Мікрофібра'] },
  ];


  const brands = ['Kimberly-Clark', 'Mirka', 'DuPont', '3M', 'Sika', 'Dettol', 'Vika', 'Norton', 'Tork', 'Henkel'];

  const products = [
      { id: 1, name: 'Диспенсер паперових рушників Kimberly-Clark 9960', price: 1099, oldPrice: null, badge: 'Хіт',     sku: 'SE50281', rating: 4.2, reviews: 12,  inStock: 150 },
      { id: 2, name: 'Активатор системи дозування X Pro 5л',              price: 450,  oldPrice: 580,  badge: 'Акція',   sku: 'AX-001',  rating: 4.8, reviews: 34,  inStock: 43  },
      { id: 3, name: 'Абразивний диск Mirka Abranet P120 150мм',          price: 89,   oldPrice: null, badge: null,      sku: 'MA-120',  rating: 4.5, reviews: 67,  inStock: 280 },
      { id: 4, name: 'Захисний комбінезон DuPont Tyvek 400 XL',           price: 320,  oldPrice: null, badge: null,      sku: 'DT-400',  rating: 4.1, reviews: 23,  inStock: 62  },
      { id: 5, name: 'Поліроль 3M Perfect-It III 1000мл',                 price: 680,  oldPrice: 820,  badge: 'Акція',   sku: '3M-P3',   rating: 4.9, reviews: 89,  inStock: 15  },
      { id: 6, name: 'Клей-герметик Sikaflex-221 чорний 300мл',           price: 520,  oldPrice: null, badge: null,      sku: 'SF-221',  rating: 4.3, reviews: 15,  inStock: 78  },
      { id: 7, name: 'Рідке мило антибактеріальне Dettol 5л',             price: 290,  oldPrice: null, badge: 'Новинка', sku: 'DT-5L',   rating: 4.6, reviews: 45,  inStock: 120 },
      { id: 8, name: 'Ґрунтовка епоксидна Vika П-ЕФ 0.8кг',              price: 185,  oldPrice: null, badge: null,      sku: 'VP-08',   rating: 4.0, reviews: 8,   inStock: 34  },
  ];

  const filteredProducts = computed(() => {
      if (activeTab.value === 'sale')    return products.filter(p => p.badge === 'Акція');
      if (activeTab.value === 'new')     return products.filter(p => p.badge === 'Новинка');
      return products;
  });
  </script>
