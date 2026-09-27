<?php

namespace Tests\Feature;

use Illuminate\Http\Middleware\TrustProxies;
use Tests\TestCase;

/** За проксі (GitHub Codespaces) редіректи мають вести на справжній домен, а не на localhost. */
class TrustedProxiesTest extends TestCase
{
    private const FORWARDED = [
        'X-Forwarded-Host'  => 'demo-8000.app.github.dev',
        'X-Forwarded-Proto' => 'https',
        'X-Forwarded-Port'  => '443',
    ];

    protected function tearDown(): void
    {
        putenv('TRUSTED_PROXIES');
        TrustProxies::flushState();
        parent::tearDown();
    }

    public function test_redirect_keeps_forwarded_host_when_proxies_trusted(): void
    {
        putenv('TRUSTED_PROXIES=*');
        $this->refreshApplication();

        $this->get('/admin', self::FORWARDED)->assertRedirect('https://demo-8000.app.github.dev/admin/login');
    }

    public function test_forwarded_headers_ignored_without_trusted_proxies(): void
    {
        // APP_ENV=testing і TRUSTED_PROXIES не задано — як на проді без налаштування
        $this->get('/admin', self::FORWARDED)->assertRedirect('http://localhost/admin/login');
    }
}
