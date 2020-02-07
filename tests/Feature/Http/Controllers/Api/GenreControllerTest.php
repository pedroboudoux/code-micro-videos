<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
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
    }

    public function testStore()
    {
        $data = ['name' => 'test'];
        $response = $this->assertStore(
            $data,
            $data + [
                'is_active' => true,
                'deleted_at' => null,
            ]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = [
            'name' => 'test',
            'is_active' => false,
        ];
        $this->assertStore(
            $data,
            $data + [
                'is_active' => false,
            ]
        );
    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create(['name' => 'test', 'is_active' => false]);
        $data = ['name' => 'test', 'is_active' => true];
        $response = $this->assertUpdate($data, $data+['deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = ['name' => 'test2'];
        $this->assertUpdate($data, array_merge($data));
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);

        $response = $this->get(route('api.genres.show', ['genre' => $this->genre->id]));
        $response->assertStatus(404);

        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function  routeStore(){
        return route('api.genres.store');
    }

    protected function routeUpdate(){
        return route('api.genres.update', ['genre' => $this->genre->id]);
    }

    protected function model(){
        return Genre::class;
    }

}
