<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CastMemberTest extends TestCase
{

    use DatabaseMigrations, TestValididationsTrait;

    public function testList()
    {
        factory(CastMember::class)->create();
        $cast_members = CastMember::all();

        $cast_memberKeys = array_keys($cast_members->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'type', 'created_at', 'updated_at', 'deleted_at'],
            $cast_memberKeys
        );

        $this->assertCount(1, $cast_members);
    }

    public function testCreateIsDirector()
    {
        $cast_member = CastMember::create(['name' => 'teste2', 'type' => CastMember::TYPE_DIRECTOR]);
        $this->assertEquals(CastMember::TYPE_DIRECTOR, $cast_member->type);
    }

    public function testCreateIsActor()
    {
        $cast_member = CastMember::create(['name' => 'teste2', 'type' => CastMember::TYPE_ACTOR]);
        $this->assertEquals(CastMember::TYPE_ACTOR, $cast_member->type);
    }

    public function testUpdate()
    {
        /** @var CastMember $cast_member */
        $cast_member = factory(CastMember::class)->create();
        $data = ['name' => 'test_name_updated', 'type' => CastMember::TYPE_ACTOR];
        $cast_member->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $cast_member->{$key});
        }
    }

    public function testDelete()
    {
        /** @var CastMember $cast_member */
        $cast_member = factory(CastMember::class)->create();
        $cast_member->delete();

        $this->assertEquals(0, CastMember::count());
    }

    public function testValidUuid()
    {
        /** @var CastMember $cast_member */
        $cast_member = factory(CastMember::class)->create();
        $this->isValidUuid($cast_member->id);
    }

}
