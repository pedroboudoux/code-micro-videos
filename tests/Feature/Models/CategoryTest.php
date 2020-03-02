<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations, TestValididationsTrait;

    public function testList()
    {
        factory(Category::class)->create();
        $categories = Category::all();

        $categoryKeys = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $categoryKeys
        );

        $this->assertCount(1, $categories);
    }

    public function testCreateOnlyName()
    {
        $category = Category::create(['name' => 'teste1']);
        $category->refresh();

        $this->assertEquals('teste1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
    }

    public function testCreateDescriptionNullExplicity()
    {

        $category = Category::create(['name' => 'teste2', 'description' => null]);
        $this->assertNull($category->description);
    }

    public function testCreateDescription()
    {
        $category = Category::create(['name' => 'teste2', 'description' => 'test_description']);
        $this->assertEquals('test_description', $category->description);
    }

    public function testCreateIsActiveFalse()
    {
        $category = Category::create(['name' => 'teste2', 'is_active' => false]);
        $this->assertFalse($category->is_active);
    }

    public function testCreateIsActiveTrue()
    {
        $category = Category::create(['name' => 'teste2', 'is_active' => true]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create(['description' => 'test_description']);
        $data = ['name' => 'test_name_updated', 'description' => 'test_description_updated', 'is_active' => true];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        $category->delete();

        $this->assertEquals(0, Category::count());
    }

    public function testValidUuid()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        $this->isValidUuid($category->id);
    }

}
