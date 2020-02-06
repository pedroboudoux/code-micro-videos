<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();

        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200)
                 ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();

        $response = $this->get(route('api.categories.show', ['category' => $category->id]));

        $response->assertStatus(200)
                 ->assertJson($category->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('api.categories.store'), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('api.categories.store'), ['name' => str_repeat('a', 256), 'is_active' => 'a']);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        /** @var Category $category */
        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            []
        );
        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            ['name' => str_repeat('a', 256), 'is_active' => 'a']
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([\Lang::get('validation.required', ['attribute' => 'name'])]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([\Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([\Lang::get('validation.boolean', ['attribute' => 'is active'])]);
    }

    public function testStore()
    {
        $response = $this->json(
            'POST',
            route('api.categories.store'),
            [
                'name' => 'test',
            ]
        );

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(201)
                ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json(
            'POST',
            route('api.categories.store'),
            [
                'name' => 'test',
                'is_active' => false,
                'description' => 'description'
            ]
        );

        $this->assertFalse($response->json('is_active'));
        $this->assertNotNull($response->json('description'));
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create(['name' => 'test', 'is_active' => false, 'description' => 'description']);
        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => 'description',
                'is_active' => true
            ]
        );

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment(
                [
                    'description' => 'description',
                    'is_active' => true,
                ]
            );

        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => ''
            ]
        );
        $response->assertJsonFragment(
            [
                'description' => null,
            ]
        );
    }

    public function testDestroy()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();

        $response = $this->json('DELETE', route('api.categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);

        $response = $this->get(route('api.categories.show', ['category' => $category->id]));
        $response->assertStatus(404);

        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }

}
