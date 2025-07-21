<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Study;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

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

        // Define your mock studies data here.
        // For hierarchical keywords, use the array format:
        // ["child_name", "immediate_parent_name", "grandparent_name", "great_grandparent_name", ...]
        $mockStudiesData = [
            [
                "title" => "Breast Cancer Somatic Mutation Database",
                "keywords" => [
                    "Breast", // This is a value under "Primary sites" > "Primary site"
                    ["Somatic Mutations", "Multi-omic Data", "Patient study"], // Disambiguated 3-level path
                    ["Multi-omic Data", "Patient study"], // Disambiguated 2-level path
                ],
                "data" => [
                    "somatic mutation" => [
                        "number of data points" => "3000",
                        "format" => "vcf",
                        "headings" => ["CHROM", "POS", "ID", "REF", "ALT", "QUAL", "FILTER", "INFO"],
                        "technology" => "WGS",
                        "algorithm" => "somatic sniper"
                    ]
                ]
            ],
            [
                "title" => "Colorectal Organoid Drug Screening Response Data",
                "keywords" => [
                    "Colorectal", // Value under Primary site
                    "In Vitro Study", // Category
                    ["Organoid Study", "In Vitro Study"], // Subcategory under In Vitro Study
                    ["Organoid Source", "Organoid Study", "In Vitro Study"], // Subcategory under Organoid Study
                    ["Patient", "Organoid Source", "Organoid Study", "In Vitro Study"], // Value 'Patient' under 'Organoid Source' (4-levels deep)
                ],
                "data" => [
                    "drug response" => [
                        "format" => "csv",
                        "headings" => ["Organoid_ID", "Drug_Name", "Concentration_uM", "Viability_%", "IC50"],
                        "assay_type" => "Cell viability assay",
                        "technology" => "High-throughput screening",
                        "number of data points" => "500"
                    ]
                ]
            ],
            [
                "title" => "Mouse Lung Cancer PDX Model Imaging Data (MRI)",
                "keywords" => [
                    "Lung", // Value under Primary site
                    "Mouse study", // Category
                    ["Imaging Data", "Mouse study"], // Subcategory under Mouse study
                    ["Magnetic resonance imaging", "Imaging Data", "Mouse study"], // Value under Imaging Data
                    ["Tumour Model", "Mouse study"], // Subcategory under Mouse study
                    ["Patient-Derived xenograft", "Tumour Model", "Mouse study"] // Value under Tumour Model
                ],
                "data" => [
                    "MRI scans" => [
                        "format" => "DICOM",
                        "headings" => ["Patient_ID", "Scan_Date", "Sequence", "Tumor_Volume_mm3", "Response_Category"],
                        "resolution" => "0.5mm isotropic",
                        "technology" => "3T MRI scanner",
                        "number of data points" => "150"
                    ]
                ]
            ],
            [
                "title" => "Gynaecological Cancer Patient Electronic Health Records",
                "keywords" => [
                    "Gynaecological", // Value under Primary site
                    "Patient study", // Category
                    ["Biobank Samples", "Patient study"], // Subcategory under Patient study
                    ["Tissues", "Biobank Samples", "Patient study"], // Value under Biobank Samples
                    ["Background", "Patient study"], // Subcategory under Patient study
                    ["Demographic", "Background", "Patient study"] // Value under Background
                ],
                "data" => [
                    "EHR" => [
                        "format" => "JSONL",
                        "source" => "Hospital A, B, C",
                        "headings" => ["Patient_ID", "Age_at_Diagnosis", "Ethnicity", "Cancer_Type", "Stage", "Treatment_History", "Survival_Months"],
                        "data_anonymization" => "HIPAA compliant",
                        "number of data points" => "10000"
                    ]
                ]
            ],
            [
                "title" => "Skin Cancer Population Incidence and Environmental Factors",
                "keywords" => [
                    "Skin", // Value under Primary site
                    "Population Study", // Category
                    ["Data Sources", "Population Study"], // Subcategory under Population Study
                    ["Cancer registries", "Data Sources", "Population Study"], // Value under Data Sources
                    ["Risk Factors", "Population Study"], // Subcategory under Population Study
                    ["Environmental", "Risk Factors", "Population Study"] // Value under Risk Factors
                ],
                "data" => [
                    "incidence data" => [
                        "format" => "CSV",
                        "source" => "National Cancer Registry",
                        "headings" => ["Year", "Region", "Age_Group", "Sex", "Incidence_Rate", "UV_Exposure_Index", "Air_Pollution_Level"],
                        "time_period" => "2000-2020",
                        "number of data points" => "500000"
                    ]
                ]
            ],
            [
                "title" => "Mouse Model Multi-omic Analysis",
                "keywords" => [
                    "Mouse study", // Category
                    ["Multi-omic Data", "Mouse study"], // Subcategory under Mouse study
                    ["Somatic Mutations", "Multi-omic Data", "Mouse study"] // Value under Multi-omic Data
                ],
                "data" => [
                    "analysis" => "some analysis data"
                ]
            ],
            // Added to test 4-level deep keyword linking
            [
                "title" => "Detailed In Vitro Cell Source Study",
                "keywords" => [
                    "In Vitro Study",
                    ["Organ-on-a-Chip Study", "In Vitro Study"],
                    ["Cell Source", "Organ-on-a-Chip Study", "In Vitro Study"],
                    ["Cell line", "Cell Source", "Organ-on-a-Chip Study", "In Vitro Study"] // 4-level deep keyword
                ],
                "data" => [
                    "cell_source_analysis" => "Data from cell line derived from Organ-on-a-Chip study."
                ]
            ]
        ];

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
