<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A test to fetch all Product.
     *
     * @return void
     */
    public function testFetchAllProduct()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }

    /**
     * A test to fetch single Product.
     *
     * @return void
     */
    public function testFetchSingleProduct()
    {
        $product = Product::factory()->create();
        $response = $this->get('/api/products/' . $product->id);


        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * A test to create Product.
     *
     * @return void
     */
    public function testCreateProduct()
    {
        $user = User::factory()->create();
        $token = $user->createToken(config('auth.token'))->plainTextToken;
        Storage::fake('avatars');

        // Do the request

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->post(
                '/api/products',
                [
                    'name' => 'Product 1',
                    'slug' => 'product-1',
                    'description' => 'Product 1 description',
                    'price' => 10.00,
                    'image' => UploadedFile::fake()->image('avatar.jpg'),
                    'user_id' => $user->id,
                    'quantity' => 10,
                ]
            );

        $response->assertStatus(Response::HTTP_CREATED);


        $this->assertDatabaseHas(
            'Products',
            [
                'name' => 'Product 1',
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * A test to assert missing image.
     *
     * @return void
     */
    public function testAssertMissingImage()
    {
        $user = User::factory()->create();
        $token = $user->createToken(config('auth.token'))->plainTextToken;
        Storage::fake('avatars');

        // make a file
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->post(
                '/api/products',
                [
                    'name' => 'Product 1',
                    'slug' => 'product-1',
                    'description' => 'Product 1 description',
                    'price' => 10.00,
                    'user_id' => $user->id,
                    'quantity' => 10,
                ]
            );

        Storage::disk('avatars')->assertMissing($file->hashName());
    }
}
