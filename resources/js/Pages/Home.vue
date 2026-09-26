<template>
      <AppLayout>

          <!-- 1. Hero: категорії · банер · партнерство -->
          <section class="home-hero">
              <div class="home-hero__sidebar">
                  <div class="home-hero__sidebar-title">Категорії</div>
                  <Link
                      v-for="cat in categories"
                      :key="cat.id"
                      :href="`/catalog/${cat.slug}`"
                      class="home-hero__sidebar-item"
                  >
                      <span>{{ cat.name }}</span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="m9 18 6-6-6-6"/>
                      </svg>
                  </Link>
                  <Link href="/catalog" class="home-hero__sidebar-all">Весь каталог →</Link>
              </div>

              <div class="home-hero__banner">
                  <ImgPlaceholder :h="440" label="головний банер" :seed="`hero-${slide}`" />
                  <div class="home-hero__banner-overlay">
                      <div class="home-hero__banner-label">Промислові товари</div>
                      <h1 class="home-hero__banner-title">
                          Надаємо широкий<br>асортимент продукції
                      </h1>
                      <p class="home-hero__banner-desc">
                          Для комплексного обслуговування підприємств малярно-кузовного ремонту, промислових та виробничих підприємств.
                      </p>
                      <div class="home-hero__banner-btns">
                          <Link href="/catalog" class="btn btn--primary">Замовити</Link>
                          <Link href="/catalog" class="btn btn--secondary">До каталогу</Link>
                      </div>
                  </div>
                  <div class="home-hero__dots">
                      <button
                          v-for="i in slidesCount"
                          :key="i"
                          class="home-hero__dot"
                          :class="{ 'home-hero__dot--active': slide === i - 1 }"
                          :aria-label="`Слайд ${i}`"
                          @click="slide = i - 1"
                      ></button>
                  </div>
                  <button class="home-hero__arrow home-hero__arrow--prev" aria-label="Попередній слайд" @click="slide = Math.max(0, slide - 1)">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                  </button>
                  <button class="home-hero__arrow home-hero__arrow--next" aria-label="Наступний слайд" @click="slide = Math.min(slidesCount - 1, slide + 1)">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                  </button>
              </div>

              <div class="home-hero__partner">
                  <ImgPlaceholder :h="130" label="фото партнерства" />
                  <div class="home-hero__partner-body">
                      <div class="home-hero__partner-title">Партнерство</div>
                      <p class="home-hero__partner-desc">
                          Вигідні умови для дилерів та оптових покупців. Гнучка система знижок від 5%.
                      </p>
                      <Link href="/partners" class="btn btn--outline btn--full">Стати партнером →</Link>
                  </div>
              </div>
          </section>

          <!-- 2. Бренди -->
          <section class="home-brands">
              <div class="home-brands__bar">
                  <span class="home-brands__label">Бренди</span>
                  <div class="home-brands__divider"></div>
                  <Link
                      v-for="brand in brands"
                      :key="brand.id"
                      :href="`/catalog?brand[]=${brand.id}`"
                      class="home-brands__item"
                  >{{ brand.name }}</Link>
                  <div class="home-brands__all">
                      <Link href="/brands" class="btn btn--ghost">Дивитись усі бренди →</Link>
                  </div>
              </div>
          </section>

          <!-- 3. Популярні категорії -->
          <section class="home-categories">
              <div class="section-header">
                  <div class="section-header__text">
                      <h2 class="section-header__title">Популярні категорії</h2>
                      <p class="section-header__subtitle">Понад 5 000 товарів у наявності</p>
                  </div>
                  <Link href="/catalog" class="btn btn--ghost">Дивитись все →</Link>
              </div>
              <div class="home-categories__grid">
                  <CategoryCard
                      v-for="cat in (showAllCategories ? categories : categories.slice(0, 6))"
                      :key="cat.id"
                      :category="cat"
                  />
              </div>
              <div v-if="!showAllCategories && categories.length > 6" class="home-categories__more">
                <button class="btn btn--outline" @click="showAllCategories = true">
                    Показати ще
                </button>
            </div>
          </section>

          <!-- 4. Каталог з табами -->
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
              <div v-if="filteredProducts.length" class="home-products__grid">
                  <ProductCard
                      v-for="product in filteredProducts.slice(0, 8)"
                      :key="product.id"
                      :product="product"
                  />
              </div>
              <p v-else class="home-products__empty">У цій добірці поки немає товарів.</p>
              <div class="home-categories__more">
                  <Link href="/catalog" class="btn btn--outline">Показати ще</Link>
              </div>
          </section>

          <!-- 5. Послуги -->
          <section class="home-section">
              <h2 class="section-header__title home-section__title">Послуги</h2>
              <div class="home-services">
                  <Link v-for="s in services" :key="s.n" :href="s.href" class="home-services__card">
                      <div class="home-services__num">{{ s.n }}</div>
                      <div class="home-services__title">{{ s.title }}</div>
                      <div class="home-services__desc">{{ s.desc }}</div>
                      <span class="home-more">Детальніше →</span>
                  </Link>
              </div>
              <div class="home-categories__more">
                  <Link href="/services" class="btn btn--outline">Дивитись всі послуги</Link>
              </div>
          </section>

          <!-- 6. Партнерство -->
          <section class="home-section">
              <div class="home-partners">
                  <div v-for="p in partnerOffers" :key="p.title" class="home-partners__item">
                      <div class="home-partners__title">{{ p.title }}</div>
                      <div class="home-partners__desc">{{ p.desc }}</div>
                      <Link href="/partners" class="home-more">Детальніше →</Link>
                  </div>
              </div>
              <div class="home-dealer">
                  <div>
                      <div class="home-dealer__title">Вигідні умови для дилерів</div>
                      <div class="home-dealer__text">
                          Перелік вигідних умов:
                          <template v-for="perk in dealerPerks" :key="perk"><br>— {{ perk }}</template>
                      </div>
                      <Link href="/partners" class="btn btn--primary">Детальніше →</Link>
                  </div>
                  <div class="home-dealer__img">
                      <ImgPlaceholder :h="160" label="банер партнерства" />
                  </div>
              </div>
          </section>

          <!-- 7. Про компанію -->
          <section class="home-section">
              <h2 class="section-header__title home-section__title">Про компанію</h2>
              <div class="home-about">
                  <div>
                      <div class="home-about__stats">
                          <div v-for="[num, label] in aboutStats" :key="num" class="home-about__stat">
                              <div class="home-about__num">{{ num }}</div>
                              <div class="home-about__label">{{ label }}</div>
                          </div>
                      </div>
                      <p class="home-about__text">{{ aboutText }}</p>
                      <Link href="/about" class="btn btn--outline home-about__btn">Детальніше</Link>
                  </div>
                  <div class="home-about__img">
                      <ImgPlaceholder :h="280" label="фото компанії" />
                  </div>
              </div>
          </section>

          <!-- 8. Блог та новини -->
          <section class="home-section">
              <div class="section-header">
                  <h2 class="section-header__title">Блог та новини</h2>
                  <Link href="/blog" class="btn btn--ghost">Дивитись всі новини →</Link>
              </div>
              <div class="home-blog">
                  <Link v-for="(post, i) in posts" :key="i" href="/blog" class="home-blog__card">
                      <ImgPlaceholder :h="140" label="фото новини" :seed="`${post.title}-${i * 7}`" />
                      <div class="home-blog__body">
                          <div class="home-blog__date">{{ post.date }}</div>
                          <div class="home-blog__title">{{ post.title }}</div>
                          <span class="home-more home-more--sm">Читати →</span>
                      </div>
                  </Link>
              </div>
          </section>

      </AppLayout>
  </template>

  <script setup>
  import { ref, computed } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import ProductCard from '@/Components/ProductCard.vue';
  import CategoryCard from '@/Components/CategoryCard.vue';
  import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
  import { services, partnerOffers, dealerPerks, aboutStats, aboutText, posts } from '@/data/home';

  const props = defineProps({
      categories: Array,
      brands:     Array,
      products:   Array,
  });

  const slidesCount = 4;
  const slide = ref(0);

  const activeTab = ref('popular');
  const showAllCategories = ref(false);

  const tabs = [
      { key: 'popular',  label: 'Популярні'      },
      { key: 'sale',     label: 'Акційні'         },
      { key: 'preorder', label: 'Передзамовлення' },
  ];

  const filteredProducts = computed(() => {
      if (activeTab.value === 'sale')     return props.products.filter(p => p.oldPrice || p.badge?.name === 'Акція');
      if (activeTab.value === 'preorder') return props.products.filter(p => p.stock === 0);
      return props.products;
  });
  </script>
