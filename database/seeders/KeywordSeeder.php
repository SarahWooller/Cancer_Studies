<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Keyword::truncate();

        $jsonPath = database_path('data/keywords.json');

        // Check if the file exists before attempting to read it.
        if (!File::exists($jsonPath)) {
            echo "The keywords.json file was not found at: {$jsonPath}\n";
            return;
        }

        // Get the contents of the JSON file.
        $jsonContents = File::get($jsonPath);

        // Decode the JSON content into a PHP associative array.
        $keywordsData = json_decode($jsonContents, true);

        // Check for JSON decoding errors.
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON file: " . json_last_error_msg() . "\n";
            return;
        }

        // Iterate through the loaded data and seed the keywords.
        foreach ($keywordsData as $topLevelArray) {
            foreach ($topLevelArray as $categoryName => $categoryData) {
                // The recursion starts here, handling all subsequent nesting.
                $this->seedKeywordNode($categoryName, $categoryData, null);
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Seeds a keyword node and its children recursively.
     *
     * @param string $keywordName The name of the current keyword.
     * @param array|string $childrenData The data for its children, or the value itself if it's a leaf.
     * @param int|null $parentId The ID of the parent keyword, if applicable.
     */
    protected function seedKeywordNode(string $keywordName, $childrenData, ?int $parentId): void
    {
        $type = null;

        if ($parentId === null) {
            $type = 'category';
        } elseif (is_array($childrenData) && !empty($childrenData) && !is_numeric(array_key_first($childrenData))) {
            $type = 'subcategory'; // Has children and the first child key is a string (e.g., "Cell Line Type")
        } elseif (is_array($childrenData) && !empty($childrenData) && is_numeric(array_key_first($childrenData))) {
             // This is an array of values, e.g., ["Breast", "Central Nervous System"]
             // The $keywordName (e.g., "Primary sites") is a subcategory, and its children are values
             $type = 'subcategory';
        } else {
            $type = 'value'; // It's a simple string value, a leaf node
        }

        // Use parent_id in the firstOrCreate condition to correctly identify unique keywords
        $keyword = Keyword::firstOrCreate(
            ['keyword' => $keywordName, 'parent_id' => $parentId],
            ['type' => $type]
        );

        if (is_array($childrenData)) {
            if (!empty($childrenData) && !is_numeric(array_key_first($childrenData))) {
                foreach ($childrenData as $childName => $grandChildrenData) {
                    $this->seedKeywordNode($childName, $grandChildrenData, $keyword->id);
                }
            } else {
                foreach ($childrenData as $childValue) {
                    $this->seedKeywordNode($childValue, null, $keyword->id); // Pass null as childrenData for leaf
                }
            }
        }
    }
}
