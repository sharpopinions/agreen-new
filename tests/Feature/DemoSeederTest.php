<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_demo_data_fills_paginated_lists_and_is_idempotent(): void
    {
        Storage::fake('local');

        $this->seed(DemoSeeder::class);
        $products = Product::count();
        $this->seed(DemoSeeder::class);

        $this->assertSame($products, Product::count());
        $this->assertGreaterThan(24, Product::where('sku', 'like', 'DM-%')->count());
        $this->assertGreaterThan(12, BlogPost::count());

        $client = User::where('email', 'client@demo.test')->first();
        $this->assertGreaterThan(10, $client->orders()->count());
        $this->assertGreaterThan(0, $client->documents()->count());
        $this->assertGreaterThan(0, $client->cashbackTransactions()->count());
        $this->assertGreaterThan(0, User::whereNotNull('requested_role')->count());

        $this->get('/catalog?page=2')->assertOk();
        $this->actingAs($client)->get('/account/orders?page=2')->assertOk();
        $this->assertTrue(Order::whereNull('user_id')->exists());
    }
}
