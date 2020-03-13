<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Models\Genre;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $genre;
    protected $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();

        $this->sendData = ['name' => 'test'];
    }

    public function testIndex()
    {
        $response = $this->get(route('api.genres.index'));

        $response->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.genres.show', ['genre' => $this->genre->id]));

        $response->assertStatus(200)
            ->assertJson($this->genre->toArray());
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

        $data = [
            'categories_id' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100],
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testSave()
    {
        $category = factory(Category::class)->create();

        $data = [
            [
                'send_data' => $this->sendData + ['categories_id' => [$category->id]],
                'test_data' => $this->sendData + ['is_active' => true],
            ],
            [
                'send_data' => $this->sendData + ['categories_id' => [$category->id], 'is_active' => false],
                'test_data' => $this->sendData + ['is_active' => false],
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore($value['send_data'], $value['test_data'] + ['deleted_at' => null]);
            $response->assertJsonStructure(['created_at', 'updated_at']);

            $response = $this->assertUpdate($value['send_data'], $value['test_data'] + ['deleted_at' => null]);
            $response->assertJsonStructure(['created_at', 'updated_at']);
        }
    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('findOrFail')->withAnyArgs()->andReturn($this->genre);
        $controller->shouldReceive('validate')->withAnyArgs()->andReturn(['name' => 'test']);
        $controller->shouldReceive('rulesUpdate')->withAnyArgs()->andReturn([]);
        $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException('gg'));

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        try {
            $controller->update($request, $this->genre->id);
        } catch (TestException $e) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(GenreController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('validate')->withAnyArgs()->andReturn($this->sendData);
        $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);
        $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException('gg'));

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestException $e) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];
        $response = $this->json('POST', $this->routeStore(), $sendData);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];
        $response = $this->json('PUT', route('api.genres.update', ['genre' => $response->json('id')]), $sendData);
        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('id')
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id' => $response->json('id')
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[2],
            'genre_id' => $response->json('id')
        ]);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);

        $response = $this->get(route('api.genres.show', ['genre' => $this->genre->id]));
        $response->assertStatus(404);

        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function routeStore()
    {
        return route('api.genres.store');
    }

    protected function routeUpdate()
    {
        return route('api.genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }

}
