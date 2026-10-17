<template>
    <AppLayout>
        <!-- Claude Design → BlogPostPage (стаття) і BlogEventPage (подія) -->
        <div class="info-page blog-post" :class="{ 'blog-post--event': post.event }">
            <nav class="breadcrumbs">
                <Link href="/" class="breadcrumbs__item">Головна</Link>
                <span class="breadcrumbs__sep">›</span>
                <Link href="/blog" class="breadcrumbs__item">Блог</Link>
                <span class="breadcrumbs__sep">›</span>
                <span class="breadcrumbs__item breadcrumbs__item--active">{{ post.title }}</span>
            </nav>

            <!-- Подія -->
            <template v-if="post.event">
                <h1 class="info-page__title info-page__title--center blog-post__event-title">{{ post.title }}</h1>
                <div class="blog-post__banner">
                    <img v-if="post.image" :src="post.image" :alt="post.title" />
                    <ImgPlaceholder v-else :h="400" :label="post.title" :seed="post.slug" />
                    <span class="blog-post__banner-badge">🎟️ Подія</span>
                </div>

                <div class="blog-event">
                    <div>
                        <div class="blog-event__meta">
                            <span v-if="post.event.date"><strong>📅 {{ post.event.date }}</strong></span>
                            <span v-if="post.event.time"><strong>🕐 {{ post.event.time }}</strong></span>
                            <span v-if="post.event.place"><strong>📍 {{ post.event.place }}</strong></span>
                        </div>

                        <template v-if="post.body">
                            <h2 class="block-section__title">Про подію</h2>
                            <div class="rich-text blog-post__body" v-html="post.body"></div>
                        </template>

                        <template v-if="post.event.program.length">
                            <h2 class="block-section__title blog-event__h2">Програма</h2>
                            <div class="doc-list">
                                <div v-for="row in post.event.program" :key="row.time + row.topic" class="blog-event__row">
                                    <span class="blog-event__time">{{ row.time }}</span>
                                    <span class="blog-event__topic">{{ row.topic }}</span>
                                </div>
                            </div>
                        </template>

                        <template v-if="post.event.speakers.length">
                            <h2 class="block-section__title blog-event__h2">Спікери</h2>
                            <div class="blog-event__speakers">
                                <div v-for="s in post.event.speakers" :key="s.name" class="blog-event__speaker">
                                    <div class="blog-event__avatar">{{ s.name.charAt(0) }}</div>
                                    <div class="blog-event__speaker-name">{{ s.name }}</div>
                                    <div v-if="s.role" class="blog-event__speaker-role">{{ s.role }}</div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <aside class="blog-event__aside">
                        <div class="blog-event__card">
                            <div class="blog-event__price-box">
                                <div class="blog-event__price-label">Участь</div>
                                <div class="blog-event__price">{{ post.event.price }}</div>
                                <div class="blog-event__price-note">
                                    Реєстрація обов'язкова.<template v-if="post.event.seats"> Місць обмежено: {{ post.event.seats }}</template>
                                </div>
                            </div>

                            <p v-if="post.event.past" class="blog-event__past">Подія вже відбулася.</p>
                            <LeadSuccess
                                v-else-if="sent"
                                title="Реєстрацію підтверджено!"
                                text="Ми надішлемо вам деталі та нагадування на email напередодні події."
                            />
                            <form v-else class="lead-form" novalidate @submit.prevent="submit({ post_id: post.id })">
                                <h3 class="lead-form__title lead-form__title--sm">Реєстрація на подію</h3>
                                <Field v-model="form.name" label="Ім'я" required placeholder="Іван Іваненко" autocomplete="name" :error="form.errors.name" />
                                <Field v-model="form.phone" label="Телефон" required type="tel" placeholder="+380 XX XXX XX XX" autocomplete="tel" :error="form.errors.phone" />
                                <Field v-model="form.email" label="Email" required type="email" placeholder="email@gmail.com" autocomplete="email" :error="form.errors.email" />
                                <Field v-model="form.company" label="Компанія" placeholder="Назва компанії" autocomplete="organization" :error="form.errors.company" />
                                <Honeypot v-model="form.website" />
                                <button class="btn btn--primary btn--full" type="submit" :disabled="form.processing">Зареєструватись</button>
                                <p class="blog-event__legal">
                                    Реєструючись, ви погоджуєтесь з <Link href="/privacy">політикою конфіденційності</Link>
                                </p>
                            </form>
                        </div>
                    </aside>
                </div>
            </template>

            <!-- Стаття -->
            <article v-else class="blog-article">
                <div class="blog-article__meta">
                    <span v-if="post.category" class="blog-card__cat">{{ post.category }}</span>
                    <span><time :datetime="post.iso">{{ post.date }}</time> · {{ post.read }} хв читання</span>
                </div>
                <h1 class="blog-article__title">{{ post.title }}</h1>
                <div class="blog-post__banner">
                    <img v-if="post.image" :src="post.image" :alt="post.title" />
                    <ImgPlaceholder v-else :h="360" label="головне фото статті" :seed="post.slug" />
                </div>
                <div class="rich-text blog-post__body" v-html="post.body"></div>
            </article>

            <section v-if="related.length" class="blog-related" :class="{ 'blog-related--wide': post.event }">
                <h3 class="blog-related__title">Схожі статті</h3>
                <div class="blog-related__grid">
                    <BlogCard v-for="r in related" :key="r.id" :post="r" small />
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BlogCard from '@/Components/BlogCard.vue';
import Field from '@/Components/Form/Field.vue';
import Honeypot from '@/Components/Form/Honeypot.vue';
import LeadSuccess from '@/Components/Form/LeadSuccess.vue';
import ImgPlaceholder from '@/Components/ImgPlaceholder.vue';
import { useLeadForm } from '@/composables/useLeadForm';

defineProps({
    post:    { type: Object, required: true },
    related: { type: Array, default: () => [] },
});

const { form, sent, submit } = useLeadForm('event', { name: '', phone: '', email: '', company: '' });
</script>
