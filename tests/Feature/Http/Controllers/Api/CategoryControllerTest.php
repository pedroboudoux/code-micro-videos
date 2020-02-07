<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200)
                 ->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.categories.show', ['category' => $this->category->id]));

        $response->assertStatus(200)
                 ->assertJson($this->category->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => '255']);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => '255']);

        $data = [
            'is_active' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = ['name' => 'test'];
        $response = $this->assertStore(
            $data,
            $data + [
                'description' => null,
                'is_active' => true,
                'deleted_at' => null
            ]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = [
            'name' => 'test',
            'is_active' => false,
            'description' => 'description'
        ];
        $this->assertStore(
            $data,
            $data + [
                'is_active' => false,
                'description' => 'description'
            ]
        );
    }

    public function testUpdate()
    {
        $this->category = factory(Category::class)->create(['name' => 'test', 'is_active' => false, 'description' => 'description']);
        $data = ['name' => 'test', 'is_active' => false, 'description' => 'description'];
        $response = $this->assertUpdate($data, $data+['deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = ['name' => 'test', 'is_active' => false, 'description' => ''];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));

        $data = ['name' => 'test', 'is_active' => false, 'description' => 'test'];
        $this->assertUpdate($data, array_merge($data, ['description' => 'test']));

        $data = ['name' => 'test', 'is_active' => false, 'description' => null];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);

        $response = $this->get(route('api.categories.show', ['category' => $this->category->id]));
        $response->assertStatus(404);

        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }


    protected function routeStore(){
        return route('api.categories.store');
    }

    protected function routeUpdate(){
        return route('api.categories.update', ['category' => $this->category->id]);
    }

    protected function model(){
        return Category::class;
    }
}
