<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CastMemberUnitTest extends TestCase
{
    private $cast_member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast_member = new CastMember();
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class,
        ];
        $cast_member_traits = array_values(class_uses(CastMember::class));

        $this->assertEquals($traits, $cast_member_traits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'type'];
        $this->assertEquals($fillable, $this->cast_member->getFillable());
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string', 'type' => 'integer'];
        $this->assertEquals($casts, $this->cast_member->getCasts());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertEqualsCanonicalizing($dates, $this->cast_member->getDates());
    }

}
