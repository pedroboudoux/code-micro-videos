<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Schema\Blueprint;

class CategoryGenreStub
{
    public static function createTable()
    {

        \Schema::create('category_stub_genre_stub', function (Blueprint $table) {
            $table->string('category_stub_id');
            $table->string('genre_stub_id');
        });

    }

    public static function dropTable()
    {
        \Schema::dropIfExists('category_stub_genre_stub');
    }
}
