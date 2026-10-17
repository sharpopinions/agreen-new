<?php

namespace Tests\Feature;

use App\Filament\Resources\VacancyResource\Pages\CreateVacancy;
use App\Models\Lead;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class VacancyTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true; // VacancySeeder: 4 вакансії

    public function test_open_vacancies_are_listed(): void
    {
        Vacancy::orderBy('id')->first()->update(['is_active' => false]);

        $this->get('/vacancies')->assertOk()->assertInertia(fn($page) => $page
            ->component('Vacancies')
            ->has('vacancies', 3)
            ->where('vacancies.0.title', 'Технічний спеціаліст')
            ->has('vacancies.0.requirements', 4));
    }

    public function test_application_creates_vacancy_lead(): void
    {
        Notification::fake();

        $this->post('/forms/vacancy', ['name' => 'Кандидат', 'phone' => '+380671112233', 'vacancy' => 'Маркетолог / SMM', 'message' => 'Досвід 3 роки'])
            ->assertSessionHas('leadSent', 'vacancy');

        $lead = Lead::firstOrFail();
        $this->assertSame(['vacancy' => 'Маркетолог / SMM'], $lead->payload);
        $this->assertSame('Досвід 3 роки', $lead->message);
    }

    public function test_admin_creates_vacancy(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'content']));

        Livewire::test(CreateVacancy::class)
            ->fillForm(['is_active' => true, 'uk_title' => 'Бухгалтер', 'uk_city' => 'Київ', 'uk_requirements' => [['text' => '1С'], ['text' => 'Досвід від 3 років']]])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->get('/vacancies')->assertInertia(fn($page) => $page
            ->where('vacancies', fn($list) => collect($list)->firstWhere('title', 'Бухгалтер')['requirements'] == ['1С', 'Досвід від 3 років']));
    }
}
