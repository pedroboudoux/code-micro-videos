<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

abstract class BasicCrudRelationedController extends BasicCrudController
{
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        $self = $this;

        $obj = \DB::transaction(function () use ($request, $validatedData, $self) {
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj, $request);
            return $obj;
        });

        $obj->refresh();

        return $obj;
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $self = $this;

        $obj = \DB::transaction(function () use ($request, $validatedData, $self, $obj) {
            $obj->update($validatedData);
            $self->handleRelations($obj, $request);
            return $obj;
        });

        return $obj;
    }

    abstract protected function handleRelations($obj, Request $request);
}
