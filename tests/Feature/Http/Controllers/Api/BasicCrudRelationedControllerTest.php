<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Http\Request;
use Tests\Stubs\Models\GenreStub;
use Tests\TestCase;
use Tests\Stubs\Models\CategoryStub;
use Tests\Stubs\Models\CategoryGenreStub;
use Tests\Stubs\Controllers\GenreControllerStub;

class BasicCrudRelationedControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        CategoryStub::dropTable();
        GenreStub::dropTable();
        CategoryGenreStub::dropTable();

        CategoryStub::createTable();
        GenreStub::createTable();
        CategoryGenreStub::createTable();

        $this->controller = new GenreControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        GenreStub::dropTable();
        CategoryGenreStub::dropTable();

        parent::tearDown();
    }



    public function testStore()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'category', 'description' => 'category_description']);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'categories_id' => [$category->id]]);

        $request->shouldReceive('get')
            ->withArgs(['categories_id'])
            ->once()
            ->andReturn([$category->id]);

        $obj = $this->controller->store($request);

        $this->assertEquals(GenreStub::find(1)->toArray(), $obj->toArray());
    }

    public function testUpdate()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'category', 'description' => 'category_description']);
        /** @var CategoryStub $category2 */
        $category2 = CategoryStub::create(['name' => 'category2', 'description' => 'category_description2']);

        /** @var GenreStub $genre */
        $genre = GenreStub::create(['name' => 'test_name', 'categories_id' => [$category->id]]);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'categories_id' => [$category->id]]);

        $request->shouldReceive('get')
            ->withArgs(['categories_id'])
            ->once()
            ->andReturn([$category->id]);

        $obj = $this->controller->update($request, $genre->id);

        $this->assertEquals(GenreStub::find(1)->toArray(), $obj->toArray());
    }

}
