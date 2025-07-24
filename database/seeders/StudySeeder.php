<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Study;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File; // <--- NEW: Import the File facade

class StudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncating related tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Study::truncate(); // Clear existing studies
        DB::table('keyword_study')->truncate(); // Clear the pivot table

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- START NEW: Read data from JSON file ---
        $jsonPath = database_path('data/studies.json'); // Path to your JSON file

        if (!File::exists($jsonPath)) {
            $this->command->error("Error: studies.json not found at {$jsonPath}");
            return; // Stop seeding if file doesn't exist
        }

        $mockStudiesData = json_decode(File::get($jsonPath), true); // Read and decode JSON

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Error decoding studies.json: " . json_last_error_msg());
            return; // Stop seeding if JSON is invalid
        }
        // --- END NEW: Read data from JSON file ---

        foreach ($mockStudiesData as $studyData) {
            $study = Study::create([
                'title' => $studyData['title'],
                'metadata' => $studyData['data'] ?? [], // Store the 'data' field as metadata JSON
            ]);

            $attachedKeywordCount = 0;
            foreach ($studyData['keywords'] as $keywordEntry) {
                $keyword = null;

                if (is_array($keywordEntry)) {
                    // Path is provided as [child, immediate_parent, grandparent, ...]
                    // Reverse it for the recursive search function to work from top-level down
                    $pathForSearch = array_reverse($keywordEntry);
                    $keyword = $this->findKeywordByPath($pathForSearch);
                } else {
                    // This is a simple, unambiguous keyword entry (just the name string)
                    $keyword = Keyword::where('keyword', $keywordEntry)->first();
                }

                // Attach the found keyword to the study
                if ($keyword) {
                    $study->keywords()->attach($keyword->id);
                    $attachedKeywordCount++;
                } else {
                    // Display the path in the order it was provided in mockStudiesData for clarity
                    $displayPath = is_array($keywordEntry) ? implode(" > ", $keywordEntry) : $keywordEntry;
                    $this->command->warn("Warning: Keyword path '" . $displayPath . "' not found for study '" . $studyData['title'] . "'");
                }
            }
            $this->command->info("Seeded study: " . $study->title . " with " . $attachedKeywordCount . " keywords.");
        }
    }

    /**
     * Recursively finds a keyword by its hierarchical path.
     * Path is expected as [top_level_keyword, ... , child_keyword].
     * Example: ['In Vitro Study', 'Organ-on-a-Chip Study', 'Cell Source', 'Cell line']
     *
     * @param array $path The keyword path from top-level to child.
     * @return Keyword|null
     */
    protected function findKeywordByPath(array $path): ?Keyword
    {
        if (empty($path)) {
            return null;
        }

        // The very last element in the reversed path is the target keyword (the child)
        $targetKeywordName = array_pop($path);

        // Start with the query for the target keyword
        $query = Keyword::where('keyword', $targetKeywordName);

        // If there are remaining elements, they are the parents (from immediate up to root)
        if (!empty($path)) {
            $this->buildParentHas($query, $path);
        }

        return $query->first();
    }

    /**
     * Helper to recursively build whereHas clauses for parents.
     * This function builds the chain from the immediate parent up to the root.
     *
     * @param Builder $query The current query builder instance.
     * @param array $parents The remaining parent keywords in the path (from immediate parent up to root).
     */
    protected function buildParentHas(Builder $query, array $parents): void
    {
        if (empty($parents)) {
            return;
        }

        $immediateParentName = array_pop($parents); // Get the current parent in the chain

        $query->whereHas('parent', function ($q) use ($immediateParentName, $parents) {
            $q->where('keyword', $immediateParentName);
            // Recursively call for higher-level parents if they exist
            if (!empty($parents)) {
                $this->buildParentHas($q, $parents);
            }
        });
    }
}
