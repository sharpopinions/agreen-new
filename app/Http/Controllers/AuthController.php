<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\EmailCodes;
use App\Support\Seo;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Вхід, реєстрація, підтвердження email кодом, вибір ролі, скидання пароля
 * (Claude Design → auth-modal.jsx). Відповіді — redirect back з flash auth.step,
 * вікно входу переходить на потрібний крок.
 */
class AuthController extends Controller
{
    public function __construct(private EmailCodes $codes) {}

    /** Сторінка входу — для посилань і редіректу з закритих сторінок (/account). */
    public function loginPage(): Response|RedirectResponse
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('account');
        }

        return Inertia::render('Login', ['seo' => Seo::make('Вхід до особистого кабінету', noindex: true)]);
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge([
            'phone' => User::normalizePhone($request->input('phone')),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone'     => ['required', 'regex:/^\+\d{11,13}$/', Rule::unique(User::class, 'phone')],
            'email'     => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password'  => ['required', 'confirmed', PasswordRule::min(8)],
            'policy'    => ['accepted'],
        ], [
            'phone.regex'     => 'Вкажіть телефон у форматі +380 XX XXX XX XX.',
            'phone.unique'    => 'Цей телефон уже зареєстровано. Увійдіть або відновіть пароль.',
            'email.unique'    => 'Цей email уже зареєстровано. Увійдіть або відновіть пароль.',
            'policy.accepted' => 'Потрібна згода з політикою конфіденційності.',
        ], ['last_name' => 'прізвище', 'password' => 'пароль']);

        $user = User::create([
            'name'      => $data['name'],
            'last_name' => $data['last_name'],
            'phone'     => $data['phone'],
            'email'     => Str::lower($data['email']),
            'password'  => $data['password'],
            'role'      => 'client',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->codes->send($user);

        return back()->with('auth', ['step' => 'confirm', 'email' => $user->email]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']], [], ['code' => 'код']);
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('auth', ['step' => 'role']);
        }

        if (! $this->codes->verify($user, $data['code'])) {
            throw ValidationException::withMessages(['code' => 'Невірний або прострочений код. Перевірте лист або надішліть код ще раз.']);
        }

        return back()->with('auth', ['step' => 'role']);
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return back()->with('auth', ['step' => 'role']);
        }

        if (($wait = $this->codes->cooldown($user)) > 0) {
            throw ValidationException::withMessages(['code' => "Надіслати ще раз можна через {$wait} с."]);
        }

        $this->codes->send($user);

        return back()->with('auth', ['step' => 'confirm', 'email' => $user->email, 'resent' => true]);
    }

    /** Крок «Оберіть, як ви плануєте користуватися платформою». */
    public function chooseRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role'         => ['required', Rule::in(['client', 'business_client', 'business_partner', 'undecided'])],
            'company'      => ['nullable', 'required_if:role,business_client,business_partner', 'string', 'max:255'],
            'company_code' => ['nullable', 'regex:/^\d{8,10}$/'],
        ], ['company_code.regex' => 'ЄДРПОУ — 8 цифр, ІПН — 10.'], ['company' => 'назва компанії', 'company_code' => 'ЄДРПОУ / ІПН']);

        $user = $request->user();
        abort_unless($user->hasVerifiedEmail(), 403);

        if (in_array($data['role'], User::BUSINESS_ROLES, true)) {
            // Бізнес-роль — лише після перевірки менеджером (адмінка → Користувачі)
            $user->update([
                'company'           => $data['company'],
                'company_code'      => $data['company_code'] ?? null,
                'requested_role'    => $data['role'],
                'role_requested_at' => now(),
            ]);
            $this->lead($user, 'partner', [
                'partner_type' => __('admin.users.roles.' . $data['role'], [], 'uk'),
            ], 'Заявка на бізнес-роль з реєстрації');
        } elseif ($data['role'] === 'undecided') {
            $this->lead($user, 'contact', [], 'Новий клієнт не визначився з роллю — потрібна консультація.');
        }

        return redirect()->route('account')->with('auth', ['step' => 'done', 'role' => $data['role']]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ], [], ['login' => 'email або телефон', 'password' => 'пароль']);

        $field = str_contains($data['login'], '@') ? 'email' : 'phone';
        $value = $field === 'email' ? Str::lower(trim($data['login'])) : User::normalizePhone($data['login']);

        if (! Auth::attempt([$field => $value, 'password' => $data['password']], (bool) ($data['remember'] ?? false))) {
            throw ValidationException::withMessages(['login' => 'Невірний email / телефон або пароль.']);
        }

        $request->session()->regenerate();
        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();

        // Не підтвердив email при реєстрації — показуємо крок з кодом
        if (! $user->hasVerifiedEmail()) {
            if ($this->codes->cooldown($user) === 0) {
                $this->codes->send($user);
            }

            return back()->with('auth', ['step' => 'confirm', 'email' => $user->email]);
        }

        return redirect()->intended($user->isStaff() ? '/admin' : route('account'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /** «Забули пароль?» — лист з посиланням. Відповідь однакова, чи є такий email, чи ні. */
    public function forgot(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        Password::sendResetLink(['email' => Str::lower($request->input('email'))]);

        return back()->with('auth', ['step' => 'forgot_sent']);
    }

    public function resetPage(Request $request, string $token): Response
    {
        return Inertia::render('ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email'),
            'seo'   => Seo::make('Новий пароль', noindex: true),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [], ['password' => 'пароль']);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
                // Лист прийшов на цю пошту — отже, вона підтверджена
                if (! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => 'Посилання недійсне або застаріло. Запросіть нове.']);
        }

        return redirect()->route('login')->with('auth', ['step' => 'reset_done']);
    }

    private function lead(User $user, string $type, array $payload, string $message): void
    {
        Lead::create([
            'type'    => $type,
            'user_id' => $user->id,
            'name'    => $user->fullName(),
            'phone'   => $user->phone,
            'email'   => $user->email,
            'company' => $user->company,
            'message' => $message,
            'payload' => $payload ?: null,
        ]);
    }
}
