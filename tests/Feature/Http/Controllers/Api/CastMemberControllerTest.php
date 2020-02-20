<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('api.cast_members.index'));

        $response->assertStatus(200)
            ->assertJson([$this->castMember->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.cast_members.show', ['cast_member' => $this->castMember->id]));

        $response->assertStatus(200)
            ->assertJson($this->castMember->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'type' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => '255']);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => '255']);

        $data = [
            'type' => 'aaa',
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testStore()
    {
        $data = ['name' => 'test', 'type' => CastMember::TYPE_ACTOR,];
        $response = $this->assertStore(
            $data,
            $data + [
                'deleted_at' => null,
            ]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testUpdate()
    {
        $this->castMember = factory(CastMember::class)->create(['name' => 'test', 'type' => CastMember::TYPE_ACTOR]);
        $data = ['name' => 'test', 'type' => CastMember::TYPE_DIRECTOR];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = ['name' => 'test2', 'type' => CastMember::TYPE_ACTOR];
        $this->assertUpdate($data, array_merge($data));
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);

        $response = $this->get(route('api.cast_members.show', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(404);

        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    protected function routeStore()
    {
        return route('api.cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('api.cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function model()
    {
        return CastMember::class;
    }

}
