<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudRelationedController;
use Illuminate\Http\Request;
use Tests\Stubs\Models\GenreStub;

class GenreControllerStub extends BasicCrudRelationedController
{
    protected function model()
    {
        return GenreStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:category_stubs,id'
        ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:category_stubs,id'
        ];
    }

    protected function handleRelations($genre, Request $request)
    {
        /** @var GenreStub $genre */
        $genre->categories()->sync($request->get('categories_id'));
    }
}
