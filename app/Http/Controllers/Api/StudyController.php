<?php

namespace App\Http\Controllers\Api;

use App\Models\Study;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudyController extends \App\Http\Controllers\Controller
{
    public function filterStudies(Request $request): JsonResponse
    {
        $query = Study::query();

        // Get the keyword IDs from the request
        $keywordIds = $request->input('keywords', []);


        if (!empty($keywordIds)) {
            // IMPORTANT CHANGE: Filter by keyword IDs, not names
            $query->whereHas('keywords', function ($q) use ($keywordIds) {
                $q->whereIn('keyword_id', $keywordIds); // 'keyword_id' is the column in the pivot table
            });
        }

        $studies = $query->get();


        return response()->json(['data' => $studies]);
    }
}
