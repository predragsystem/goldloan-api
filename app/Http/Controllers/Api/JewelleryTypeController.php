<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JewelleryTypeRequest;
use App\Models\JewelleryType;

class JewelleryTypeController extends Controller
{
    public function index()
    {
        return JewelleryType::orderBy('name')->get();
    }

    public function store(JewelleryTypeRequest $request)
    {
        return response()->json(JewelleryType::create($request->validated()), 201);
    }

    public function update(JewelleryTypeRequest $request, JewelleryType $jewelleryType)
    {
        $jewelleryType->update($request->validated());

        return $jewelleryType;
    }

    public function destroy(JewelleryType $jewelleryType)
    {
        $jewelleryType->delete();

        return response()->json(status: 204);
    }
}
