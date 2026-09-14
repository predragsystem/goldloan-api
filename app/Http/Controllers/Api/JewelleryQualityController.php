<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JewelleryQualityRequest;
use App\Models\JewelleryQuality;

class JewelleryQualityController extends Controller
{
    public function index()
    {
        return JewelleryQuality::orderBy('name')->get();
    }

    public function store(JewelleryQualityRequest $request)
    {
        return response()->json(JewelleryQuality::create($request->validated()), 201);
    }

    public function update(JewelleryQualityRequest $request, JewelleryQuality $jewelleryQuality)
    {
        $jewelleryQuality->update($request->validated());

        return $jewelleryQuality;
    }

    public function destroy(JewelleryQuality $jewelleryQuality)
    {
        $jewelleryQuality->delete();

        return response()->json(status: 204);
    }
}
