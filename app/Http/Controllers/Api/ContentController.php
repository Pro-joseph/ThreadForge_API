<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function repurpose(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
        'raw_content' => ['required', 'string'],
        'campaign_blueprint_id' => ['required', 'integer', 'exists:campaign_blueprints,id'],
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $blueprint = $request->user()->campaignBlueprints()
        ->findOrFail($request->campaign_blueprint_id);

    RepurposeContentJob::dispatch(
        rawContent: $request->raw_content,
        userId: $request->user()->id,
        campaignBlueprintId: $blueprint->id,
    );

    }
}
