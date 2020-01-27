<?php

namespace App\Models\Traits;

use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{

    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($obj) {
                $obj->id = RamseyUuid::uuid4();
            }
        );
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
