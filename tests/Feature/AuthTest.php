<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\EmailCode;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private array $registration = [
        'name' => 'Іван', 'last_name' => 'Іваненко', 'phone' => '067 123 45 67',
        'email' => 'Ivan@Example.com', 'password' => 'secret123', 'password_confirmation' => 'secret123', 'policy' => true,
    ];

    /** Код з листа (перехоплений Notification::fake). */
    private function sentCode(User $user): string
    {
        $code = null;
        Notification::assertSentTo($user, EmailCode::class, function (EmailCode $n) use (&$code) {
            $code = $n->code;

            return true;
        });

        return $code;
    }

    public function test_full_registration_flow_for_client(): void
    {
        Notification::fake();

        $this->post('/auth/register', $this->registration)->assertSessionHas('auth.step', 'confirm');

        $user = User::where('email', 'ivan@example.com')->firstOrFail();
        $this->assertSame('+380671234567', $user->phone);
        $this->assertSame('client', $user->role);
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());

        // Кабінет закритий, поки email не підтверджено
        $this->get('/account')->assertRedirect(route('verification.notice'));

        $this->post('/auth/verify', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->post('/auth/verify', ['code' => $this->sentCode($user)])->assertSessionHas('auth.step', 'role');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $this->post('/auth/role', ['role' => 'client'])->assertRedirect(route('account'));
        $this->get('/account')->assertOk()->assertInertia(fn($page) => $page
            ->component('Account/Catalog')
            ->where('auth.user.name', 'Іван')
            ->where('auth.user.verified', true));
    }

    public function test_business_role_is_a_request_for_manager(): void
    {
        Notification::fake();
        $this->post('/auth/register', $this->registration);
        $user = User::where('email', 'ivan@example.com')->firstOrFail();
        $this->post('/auth/verify', ['code' => $this->sentCode($user)]);

        $this->post('/auth/role', ['role' => 'business_partner'])->assertSessionHasErrors('company');
        $this->post('/auth/role', ['role' => 'business_partner', 'company' => 'ТОВ Фарби', 'company_code' => '12345678'])
            ->assertRedirect(route('account'));

        $user->refresh();
        $this->assertSame('client', $user->role, 'Бізнес-роль — лише після підтвердження менеджером');
        $this->assertSame('business_partner', $user->requested_role);
        $this->assertSame('ТОВ Фарби', $user->company);
        $this->assertSame('partner', Lead::where('user_id', $user->id)->value('type'));
    }

    public function test_duplicate_email_or_phone_and_policy_required(): void
    {
        User::factory()->create(['email' => 'ivan@example.com', 'phone' => '+380671234567']);

        $this->post('/auth/register', $this->registration)->assertSessionHasErrors(['email', 'phone']);
        $this->post('/auth/register', ['policy' => false] + $this->registration)->assertSessionHasErrors('policy');
        $this->assertGuest();
    }

    public function test_login_by_email_or_phone_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'buyer@example.com', 'phone' => '+380501112233', 'password' => 'secret123', 'role' => 'client']);

        $this->post('/auth/login', ['login' => 'buyer@example.com', 'password' => 'wrong'])->assertSessionHasErrors('login');
        $this->assertGuest();

        $this->post('/auth/login', ['login' => '050 111 22 33', 'password' => 'secret123'])->assertRedirect(route('account'));
        $this->assertAuthenticatedAs($user);

        $this->post('/auth/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/auth/login', ['login' => 'BUYER@example.com', 'password' => 'secret123'])->assertRedirect(route('account'));
    }

    public function test_unverified_login_goes_to_code_step(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['password' => 'secret123', 'role' => 'client']);

        $this->post('/auth/login', ['login' => $user->email, 'password' => 'secret123'])->assertSessionHas('auth.step', 'confirm');
        Notification::assertSentTo($user, EmailCode::class);
    }

    public function test_code_attempts_are_limited(): void
    {
        Notification::fake();
        $this->post('/auth/register', $this->registration);
        $user = User::where('email', 'ivan@example.com')->firstOrFail();
        $code = $this->sentCode($user);

        foreach (range(1, 5) as $_) {
            $this->post('/auth/verify', ['code' => '111111']);
        }
        // Після 5 невдалих спроб навіть правильний код не приймається — треба надіслати новий
        $this->post('/auth/verify', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_password_reset(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'buyer@example.com', 'role' => 'client']);

        $this->post('/auth/forgot', ['email' => 'buyer@example.com'])->assertSessionHas('auth.step', 'forgot_sent');
        $this->post('/auth/forgot', ['email' => 'nobody@example.com'])->assertSessionHas('auth.step', 'forgot_sent');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($n) use (&$token) {
            $token = $n->token;

            return true;
        });

        $this->get("/reset-password/{$token}?email=buyer@example.com")->assertOk()->assertInertia(fn($p) => $p->component('ResetPassword'));
        $this->post('/reset-password', ['token' => $token, 'email' => 'buyer@example.com', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertRedirect(route('login'));

        $this->post('/auth/login', ['login' => 'buyer@example.com', 'password' => 'newpass123'])->assertRedirect(route('account'));
    }

    public function test_guest_redirected_to_login_from_account(): void
    {
        $this->get('/account')->assertRedirect(route('login'));
        $this->get('/login')->assertOk()->assertInertia(fn($p) => $p->component('Login'));
    }
}
