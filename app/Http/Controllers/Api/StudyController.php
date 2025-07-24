<?php

namespace App\Http\Controllers\Api;

use App\Models\Study;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Ensure this is correctly aliased if needed

class StudyController extends \App\Http\Controllers\Controller
{
public function filterStudies(Request $request): JsonResponse
    {
        $query = Study::with('keywords'); // Still eager loading for frontend display

        // Get the keyword IDs from the request
        $keywordIds = $request->input('keywords', []);

        if (!empty($keywordIds)) {
            // Loop through each keyword ID and add a separate whereHas condition
            // This ensures that the study must be related to ALL of the selected keywords
            foreach ($keywordIds as $keywordId) {
                $query->whereHas('keywords', function ($q) use ($keywordId) {
                    $q->where('id', $keywordId);
                });
            }
        }

        $studies = $query->get();

        return response()->json(['data' => $studies]);
    }
public function show(int $id): JsonResponse
    {
        // Find the study by ID and eager load its keywords
        $study = Study::with('keywords')->find($id);

        if (!$study) {
            return response()->json(['message' => 'Study not found'], 404);
        }

        return response()->json($study); // Return the single study object directly
    }
}
