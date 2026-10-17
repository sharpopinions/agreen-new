<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true; // ServiceSeeder: 8 послуг, 3 відгуки

    private function studyCenter(): Service
    {
        return Service::whereHas('translations', fn($q) => $q->where('title', 'Навчальний центр'))->firstOrFail();
    }

    public function test_services_list_and_home_block(): void
    {
        $this->get('/services')->assertOk()->assertInertia(fn($page) => $page
            ->component('Services')
            ->has('services', 8)
            ->where('services.0.details.0', 'Теоретичні та практичні заняття')
            ->has('testimonials', 3));

        $this->get('/')->assertInertia(fn($page) => $page
            ->has('services', 4)
            ->where('services.0.title', 'Навчальний центр'));
    }

    public function test_study_center_page_and_course_application(): void
    {
        Notification::fake();
        $slug = $this->studyCenter()->translations()->value('slug');

        $this->get('/services/' . $slug)->assertOk()->assertInertia(fn($page) => $page
            ->component('Service')
            ->has('service.courses', 6)
            ->has('service.stats', 4)
            ->has('service.benefits', 4));

        $this->post('/forms/service', [
            'name' => 'Майстер', 'phone' => '+380671112233', 'service' => 'Навчальний центр', 'course' => 'Підбір та змішування кольору',
        ])->assertSessionHas('leadSent', 'service');

        $this->assertSame(['service' => 'Навчальний центр', 'course' => 'Підбір та змішування кольору'], Lead::firstOrFail()->payload);
    }

    public function test_hidden_service_404_and_admin_edits_courses(): void
    {
        $service = $this->studyCenter();
        $slug = $service->translations()->value('slug');

        $this->actingAs(User::factory()->create(['role' => 'content']));
        Livewire::test(EditService::class, ['record' => $service->id])
            ->fillForm(['uk_courses' => [['title' => 'Новий курс', 'duration' => '4 години', 'level' => '', 'price' => '900 ₴']]])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame('Новий курс', $service->translations()->first()->courses[0]['title']);

        $service->update(['is_active' => false]);
        $this->get('/services/' . $slug)->assertNotFound();
    }
}
