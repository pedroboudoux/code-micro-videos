<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Mockery\Exception;

trait TestSaves
{
    protected abstract function  model();
    protected abstract function  routeStore();
    protected abstract function  routeUpdate();

    protected function assertStore($sendData, $testDatabase, $testJsonData = []) : TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json(
            'POST',
            $this->routeStore(),
            $sendData
        );
        if($response->status() != 201){
            throw new Exception("Response status must be 201, given {$response->status()}: \n {$response->content()}");
        }
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testJsonData, $testDatabase);


        return $response;
    }

    protected function assertUpdate($sendData, $testDatabase, $testJsonData = []) : TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $sendData
        );
        if($response->status() != 200){
            throw new Exception("Response status must be 200, given {$response->status()}: \n {$response->content()}");
        }
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testJsonData, $testDatabase);

        return $response;
    }

    private function assertInDatabase($response, $testDatabase)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase+['id' => $response->json('id')]);
    }

    private function assertJsonResponseContent($response, $testJsonData, $testDatabase)
    {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse+['id' => $response->json('id')]);
    }

}
