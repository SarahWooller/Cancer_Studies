<?php

namespace App\Http\Controllers\Api;

use App\Models\Keyword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KeywordController extends Controller
{
    public function hierarchy(): JsonResponse
    {
        $topLevelKeywords = Keyword::whereNull('parent_id')
                                   ->orderBy('keyword')
                                   ->get();

        $hierarchy = $this->buildHierarchy($topLevelKeywords);

        return response()->json($hierarchy);
    }

    private function buildHierarchy($keywords): array
    {
        $result = [];
        foreach ($keywords as $keyword) {
            $children = Keyword::where('parent_id', $keyword->id)
                               ->orderBy('keyword')
                               ->get();

            $item = [
                'id' => $keyword->id, // <-- ADDED: Include the keyword ID
                'keyword' => $keyword->keyword,
                'type' => $keyword->type,
            ];

            if ($children->isNotEmpty()) {
                $item['children'] = $this->buildHierarchy($children);
            }

            $result[] = $item;
        }
        return $result;
    }
}
