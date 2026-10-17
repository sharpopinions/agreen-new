<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Language;
use App\Support\Html;
use App\Support\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Блог і події (Claude Design → BlogPage, BlogPostPage, BlogEventPage). */
class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $langId = Language::currentId();
        $tr     = fn($q) => $q->where('language_id', $langId);

        $categories = BlogCategory::with(['translations' => $tr])
            ->where('is_active', true)
            ->whereHas('posts', fn($q) => $q->published())
            ->orderBy('sort_order')->get();

        $current = $categories->first(fn($c) => $c->translations->first()?->slug === $request->query('category'));

        $posts = self::query()
            ->when($current, fn($q) => $q->where('blog_category_id', $current->id))
            ->paginate(12)
            ->withQueryString()
            ->through(fn(BlogPost $p) => self::card($p));

        return Inertia::render('Blog', [
            'posts'      => $posts,
            'categories' => $categories->map(fn($c) => ['name' => $c->translations->first()?->name, 'slug' => $c->translations->first()?->slug])
                ->filter(fn($c) => $c['slug'])->values(),
            'category'   => $current?->translations->first()?->slug,
            'seo'        => Seo::make(
                $current ? 'Блог: ' . $current->translations->first()?->name : 'Блог та новини',
                'Новини ринку, поради фахівців і події A-green',
                noindex: $request->filled('page'),
            ),
        ]);
    }

    public function show(string $slug): Response
    {
        $langId = Language::currentId();
        $post   = self::query()
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();
        $t = $post->translations->first();

        $related = self::query()
            ->whereKeyNot($post->id)
            ->when($post->blog_category_id, fn($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->limit(3)->get()
            ->map(fn(BlogPost $p) => self::card($p));

        return Inertia::render('BlogPost', [
            'post' => self::card($post) + [
                'body'  => Html::clean($t?->body),
                'event' => $post->isEvent() ? [
                    'date'     => $post->event_starts_at?->translatedFormat('j F Y'),
                    'time'     => $post->event_starts_at
                        ? $post->event_starts_at->format('H:i') . ($post->event_ends_at ? ' – ' . $post->event_ends_at->format('H:i') : '')
                        : null,
                    'place'    => $t?->event_place,
                    'price'    => $t?->event_price ?: 'Безкоштовно',
                    'seats'    => $post->event_seats,
                    'program'  => array_values($t?->program ?? []),
                    'speakers' => array_values($t?->speakers ?? []),
                    'past'     => (bool) ($post->event_ends_at ?? $post->event_starts_at)?->isPast(),
                ] : null,
            ],
            'related' => $related,
            'seo'     => Seo::make(
                $t?->meta_title ?: ($t?->title ?? ''),
                $t?->meta_description ?: ($t?->excerpt ?: $t?->body),
                $post->image_url,
                raw: (bool) $t?->meta_title,
            ),
        ]);
    }

    /** Опубліковані записи з перекладами поточною мовою, нові вгорі. */
    public static function query()
    {
        $langId = Language::currentId();
        $tr     = fn($q) => $q->where('language_id', $langId);

        return BlogPost::published()
            ->with(['translations' => $tr, 'category.translations' => $tr])
            ->whereHas('translations', fn($q) => $q->where('language_id', $langId)->whereNotNull('slug'))
            ->orderByDesc('published_at');
    }

    /** Картка запису (список, «схожі», головна). */
    public static function card(BlogPost $p): array
    {
        $t = $p->translations->first();

        return [
            'id'       => $p->id,
            'type'     => $p->type,
            'title'    => $t?->title ?? '',
            'slug'     => $t?->slug ?? '',
            'excerpt'  => $t?->excerpt ?: Seo::excerpt($t?->body, 140),
            'image'    => $p->image_url,
            'category' => $p->category?->translations->first()?->name,
            'date'     => $p->published_at?->translatedFormat('j M Y'),
            'iso'      => $p->published_at?->toIso8601String(),
            'read'     => $t ? $t->readingMinutes() : 1,
        ];
    }
}
