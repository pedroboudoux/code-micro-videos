<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryUnitTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = new Category();
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class,
        ];
        $category_traits = array_values(class_uses(Category::class));

        $this->assertEquals($traits, $category_traits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertEqualsCanonicalizing($dates, $this->category->getDates());
    }

}
