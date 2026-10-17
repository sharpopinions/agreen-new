<?php

namespace Tests\Feature;

use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Models\BlogPost;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true; // BlogSeeder: 8 статей + 1 подія

    private function slugOf(string $title): string
    {
        return BlogPost::whereHas('translations', fn($q) => $q->where('title', $title))->firstOrFail()->translations()->value('slug');
    }

    public function test_blog_list_newest_first_with_category_tabs(): void
    {
        $this->get('/blog')->assertOk()->assertInertia(fn($page) => $page
            ->component('Blog')
            ->where('posts.total', 9)
            ->where('posts.data.0.type', 'event')
            ->has('categories', 5));

        $porady = \App\Models\BlogCategoryTranslation::where('name', 'Поради')->value('slug');
        $this->get('/blog?category=' . $porady)->assertInertia(fn($page) => $page
            ->where('category', $porady)
            ->where('posts.total', 2));
    }

    public function test_scheduled_and_hidden_posts_are_not_shown(): void
    {
        $post = BlogPost::where('type', 'article')->firstOrFail();
        $slug = $post->translations()->value('slug');

        $post->update(['published_at' => now()->addDay()]);
        $this->get('/blog')->assertInertia(fn($page) => $page->where('posts.total', 8));
        $this->get('/blog/' . $slug)->assertNotFound();

        $post->update(['published_at' => now()->subDay(), 'is_active' => false]);
        $this->get('/blog/' . $slug)->assertNotFound();
    }

    public function test_article_page_with_related_posts(): void
    {
        $this->get('/blog/' . $this->slugOf('Нові технології лакофарбових покриттів у 2026 році'))
            ->assertOk()
            ->assertInertia(fn($page) => $page
                ->component('BlogPost')
                ->where('post.event', null)
                ->where('post.category', 'Новини ринку')
                ->where('related', fn($r) => collect($r)->count() === 1 && $r[0]['category'] === 'Новини ринку'));
    }

    public function test_event_page_and_registration_lead(): void
    {
        Notification::fake();
        $event = BlogPost::where('type', 'event')->firstOrFail();

        $this->get('/blog/' . $event->translations()->value('slug'))->assertInertia(fn($page) => $page
            ->where('post.event.time', '10:00 – 17:00')
            ->where('post.event.seats', 30)
            ->has('post.event.program', 6)
            ->where('post.event.past', false));

        $this->post('/forms/event', [
            'name' => 'Майстер', 'phone' => '+380671112233', 'email' => 'm@example.com', 'post_id' => $event->id,
        ])->assertSessionHas('leadSent', 'event');

        $lead = Lead::firstOrFail();
        $this->assertSame('event', $lead->type);
        $this->assertSame('Воркшоп: Сучасні технології фарбування 2026', $lead->payload['event']);

        // Реєстрація лише на події, не на статті
        $article = BlogPost::where('type', 'article')->firstOrFail();
        $this->post('/forms/event', ['name' => 'X', 'phone' => '+380671112233', 'email' => 'x@example.com', 'post_id' => $article->id])
            ->assertSessionHasErrors('post_id');
    }

    public function test_home_shows_latest_posts_and_admin_creates_event(): void
    {
        $this->get('/')->assertInertia(fn($page) => $page->has('posts', 4));

        $this->actingAs(User::factory()->create(['role' => 'content']));
        Livewire::test(CreateBlogPost::class)
            ->fillForm([
                'type'            => 'event',
                'published_at'    => now()->subHour(),
                'event_starts_at' => now()->addWeek(),
                'is_active'       => true,
                'uk_title'        => 'Вебінар з полірування',
                'uk_body'         => '<p>Онлайн</p>',
                'uk_program'      => [['time' => '12:00', 'topic' => 'Старт']],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = BlogPost::latest('id')->firstOrFail();
        $this->assertSame([['time' => '12:00', 'topic' => 'Старт']], $post->translations()->first()->program);

        // Подія без дати початку не зберігається
        Livewire::test(CreateBlogPost::class)
            ->fillForm(['type' => 'event', 'uk_title' => 'Без дати', 'event_starts_at' => null])
            ->call('create')
            ->assertHasFormErrors(['event_starts_at']);
    }
}
