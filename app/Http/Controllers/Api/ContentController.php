<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RepurposeContentJob;
use App\Models\CampaignBlueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Content Repurposing
 *
 * Transform raw notes into structured social media posts via AI.
 */
class ContentController extends Controller
{
    /**
     * Repurpose content
     *
     * Submit raw content for AI processing. The content will be transformed into a structured post
     * based on the selected campaign blueprint's rules. Processing happens asynchronously via a queue.
     * Check the generated posts endpoint for results.
     *
     * @authenticated
     *
     * @bodyParam raw_content string required The raw notes or content to transform. Example: PHP 8.4 introduced property hooks for computed properties.
     * @bodyParam campaign_blueprint_id integer required The ID of the campaign blueprint whose rules to follow. Example: 1
     *
     * @response status=202 scenario="submitted" {
     *   "message": "Content submitted for processing"
     * }
     * @response status=422 scenario="validation error" {
     *   "message": "The raw_content field is required.",
     *   "errors": { "raw_content": ["The raw_content field is required."] }
     * }
     * @response status=403 scenario="unauthorized" { "message": "This action is unauthorized." }
     */
    public function repurpose(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'raw_content' => ['required', 'string'],
            'campaign_blueprint_id' => ['required', 'integer', 'exists:campaign_blueprints,id'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $blueprint = CampaignBlueprint::findOrFail($request->campaign_blueprint_id);

        $this->authorize('view', $blueprint);

        RepurposeContentJob::dispatch(
            rawContent: $request->raw_content,
            userId: $request->user()->id,
            campaignBlueprintId: $blueprint->id,
        );

        return response()->json([
            'message' => 'Content submitted for processing',
        ], 202);
    }
}
