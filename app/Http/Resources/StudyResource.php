<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Flatten the keywords array to just their names for the frontend
        $keywords = $this->keywords->pluck('keyword')->toArray();

        return [
            'title' => $this->title,
            'keywords' => $keywords,
            'metadata' => $this->metadata, // 'metadata' is the column name in your database
        ];
    }
}
