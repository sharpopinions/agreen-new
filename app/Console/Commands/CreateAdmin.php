<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email} {--name=Admin} {--password=}';

    protected $description = 'Створити адміністратора (або надати роль admin наявному користувачу)';

    public function handle(): int
    {
        $email    = $this->argument('email');
        $password = $this->option('password') ?: $this->secret('Пароль (мін. 8 символів)');

        $validator = Validator::make(compact('email', 'password'), [
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $this->option('name'), 'password' => $password, 'role' => 'admin'],
        );

        $this->info("Адміністратор {$user->email} готовий. Вхід: /admin");

        return self::SUCCESS;
    }
}
