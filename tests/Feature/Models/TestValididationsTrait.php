<?php


namespace Tests\Feature\Models;


trait TestValididationsTrait
{

    public function isValidUuid($id)
    {
        $this->assertTrue(is_string($id));
        $this->assertTrue(
            preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $id) === 1
        );
    }

}
