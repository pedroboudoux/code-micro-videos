<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();

        $genreKeys = array_keys($genres->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $genreKeys
        );

        $this->assertCount(1, $genres);
    }

    public function testCreateOnlyName()
    {
        $genre = Genre::create(['name' => 'teste1']);
        $genre->refresh();

        $this->assertEquals('teste1', $genre->name);
        $this->assertTrue($genre->is_active);
    }

    public function testCreateIsActiveFalse()
    {
        $genre = Genre::create(['name' => 'teste2', 'is_active' => false]);
        $this->assertFalse($genre->is_active);
    }

    public function testCreateIsActiveTrue()
    {
        $genre = Genre::create(['name' => 'teste2', 'is_active' => true]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create()->first();
        $data = ['name' => 'test_name_updated', 'is_active' => true];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class, 1)->create()->first();
        $genre->delete();

        $this->assertEquals(0, Genre::count());
    }

    public function testValidUuid()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create()->first();
        $this->assertTrue(is_string($genre->id));
        $this->assertTrue(
            preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $genre->id) === 1
        );
    }

}
