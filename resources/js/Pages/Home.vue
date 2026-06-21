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
                      :key="brand.id"
                      class="home-brands__item"
                  >{{ brand.name }}</button>
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

  const props = defineProps({
      categories: Array,
      brands:     Array,
      products:   Array,
  });

  const activeTab = ref('popular');
  const showAllCategories = ref(false);

  const tabs = [
      { key: 'popular', label: 'Популярні'      },
      { key: 'sale',    label: 'Акційні'         },
      { key: 'new',     label: 'Передзамовлення' },
  ];

  const filteredProducts = computed(() => {
      if (activeTab.value === 'sale') return props.products.filter(p => p.badge === 'Акція');
      if (activeTab.value === 'new')  return props.products.filter(p => p.badge === 'Новинка');
      return props.products;
  });
  </script>
