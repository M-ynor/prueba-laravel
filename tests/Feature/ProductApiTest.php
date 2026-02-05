<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
        $this->currency = Currency::create([
            'name' => 'US Dollar',
            'symbol' => 'USD',
            'exchange_rate' => 1.000000,
        ]);
    }

    public function test_can_get_products_list(): void
    {
        Product::factory()->count(3)->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'currency',
                        'tax_cost',
                        'manufacturing_cost',
                    ]
                ],
                'meta',
                'message'
            ]);
    }

    public function test_can_create_product(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 100.00,
            'currency_id' => $this->currency->id,
            'tax_cost' => 15.00,
            'manufacturing_cost' => 50.00,
        ];

        $response = $this->postJson('/api/v1/products', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'price',
                ],
                'message'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 100.00,
        ]);
    }

    public function test_can_get_single_product(): void
    {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ],
            ]);
    }

    public function test_can_update_product(): void
    {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 150.00,
        ];

        $response = $this->putJson("/api/v1/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Updated Product Name',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 150.00,
        ]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    public function test_validation_fails_when_creating_product_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/products', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ]);
    }

    public function test_cannot_access_api_without_authentication(): void
    {
        $this->app->get('auth')->forgetGuards();

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(401);
    }
}
