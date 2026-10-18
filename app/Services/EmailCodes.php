<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailCode;
use Illuminate\Support\Facades\Hash;

/**
 * Підтвердження email 6-значним кодом: код зберігається хешем, діє 30 хв,
 * 5 спроб введення, повторна відправка — не частіше ніж раз на 60 с.
 */
class EmailCodes
{
    public const TTL_MINUTES = 30;
    public const RESEND_SECONDS = 60;
    public const MAX_ATTEMPTS = 5;

    public function send(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'email_code'          => Hash::make($code),
            'email_code_sent_at'  => now(),
            'email_code_attempts' => 0,
        ])->save();

        try {
            $user->notify(new EmailCode($code));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Скільки секунд лишилось до можливості надіслати ще раз. */
    public function cooldown(User $user): int
    {
        if (! $user->email_code_sent_at) {
            return 0;
        }

        return max(0, self::RESEND_SECONDS - (int) $user->email_code_sent_at->diffInSeconds(now()));
    }

    /** true — код вірний, email підтверджено; false — ні (лічильник спроб збільшено). */
    public function verify(User $user, string $code): bool
    {
        $expired = ! $user->email_code_sent_at || $user->email_code_sent_at->addMinutes(self::TTL_MINUTES)->isPast();

        if (! $user->email_code || $expired || $user->email_code_attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        if (! Hash::check($code, $user->email_code)) {
            $user->increment('email_code_attempts');

            return false;
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_code'        => null,
            'email_code_attempts' => 0,
        ])->save();

        return true;
    }
}
