<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Study;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        // Remember to use the array format for ambiguous keywords:
        // ["child_name", "immediate_parent_name"] for 2 levels
        // ["child_name", "immediate_parent_name", "grandparent_name"] for 3 levels
        $mockStudiesData = [
            [
                "title" => "Breast Cancer Somatic Mutation Database",
                "keywords" => [
                    "Breast",
                    "Patient study", // This is a top-level category, assume unambiguous
                    ["Multi-omic Data", "Patient study"], // Disambiguated: Multi-omic Data under Patient study
                    ["Somatic Mutations", "Multi-omic Data", "Patient study"] // Disambiguated: Somatic Mutations under Patient study's Multi-omic Data
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
                    "Colorectal",
                    "In Vitro Study",
                    "Patient", // This is a top-level category, assume unambiguous
                    ["Organoid Study", "In Vitro Study"], // Disambiguated if needed
                    ["Organoid Source", "Organoid Study", "In Vitro Study"] // Disambiguated if needed
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
                    "Lung",
                    "Mouse study", // This is a top-level category, assume unambiguous
                    ["Imaging Data", "Mouse study"], // Disambiguated if needed
                    "Magnetic resonance imaging", // This is a child of Imaging Data, so it might need full path if Imaging Data is ambiguous
                    "Tumour Model",
                    "Patient-Derived xenograft"
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
                    "Gynaecological",
                    ["Biobank Samples", "Patient study"], // Disambiguated
                    "Tissues",
                    "Patient study", // This is a top-level category, assume unambiguous
                    "Background",
                    "Demographic"
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
                    "Skin",
                    "Population Study", // This is a top-level category, assume unambiguous
                    "Data Sources",
                    "Cancer registries",
                    "Risk Factors",
                    "Environmental"
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
            // Example of a study linking to "Multi-omic Data" under "Mouse study"
            [
                "title" => "Mouse Model Multi-omic Analysis",
                "keywords" => [
                    "Mouse study",
                    ["Multi-omic Data", "Mouse study"], // Disambiguated: Multi-omic Data under Mouse study
                    ["Somatic Mutations", "Multi-omic Data", "Mouse study"] // Disambiguated: Somatic Mutations under Mouse study's Multi-omic Data
                ],
                "data" => [
                    "analysis" => "some analysis data"
                ]
            ]
        ];

        foreach ($mockStudiesData as $studyData) {
            // Create the Study record
            $study = Study::create([
                'title' => $studyData['title'],
                'metadata' => $studyData['data'] ?? [], // Store the 'data' field as metadata JSON, use null coalesce for safety
            ]);

            $attachedKeywordCount = 0;
            foreach ($studyData['keywords'] as $keywordEntry) {
                $keyword = null;

                if (is_array($keywordEntry)) {
                    // This is an ambiguous keyword entry with a path
                    $pathLength = count($keywordEntry);
                    $targetKeywordName = $keywordEntry[0]; // The actual keyword name we're looking for

                    $query = Keyword::where('keyword', $targetKeywordName);

                    // Build nested whereHas clauses for each parent in the path
                    // The path is [child, parent, grandparent, ...]
                    // We iterate from the second element (immediate parent) to the last
                    for ($i = 1; $i < $pathLength; $i++) {
                        $currentParentName = $keywordEntry[$i];
                        // Nest the whereHas to go up the hierarchy
                        $query->whereHas('parent', function ($q) use ($currentParentName, $i, $pathLength, $keywordEntry) {
                            $q->where('keyword', $currentParentName);
                            // If there are more parents in the path, nest further
                            // This part is tricky to make fully generic recursively in a loop like this.
                            // For now, we'll stick to fixed depths (2 or 3 levels) as discussed.
                            // A fully generic solution would involve a recursive helper for the query builder.
                        });
                    }

                    // Special handling for 2 and 3 level paths as discussed
                    if ($pathLength === 2) {
                        // Case: [child_name, immediate_parent_name]
                        $childKeywordName = $keywordEntry[0];
                        $immediateParentKeywordName = $keywordEntry[1];

                        $keyword = Keyword::where('keyword', $childKeywordName)
                                          ->whereHas('parent', function ($q_immediateParent) use ($immediateParentKeywordName) {
                                              $q_immediateParent->where('keyword', $immediateParentKeywordName);
                                          })
                                          ->first();
                    } elseif ($pathLength === 3) {
                        // Case: [child_name, immediate_parent_name, grandparent_name]
                        $childKeywordName = $keywordEntry[0];
                        $immediateParentKeywordName = $keywordEntry[1];
                        $grandparentKeywordName = $keywordEntry[2];

                        $keyword = Keyword::where('keyword', $childKeywordName)
                                          ->whereHas('parent', function ($q_immediateParent) use ($immediateParentKeywordName, $grandparentKeywordName) {
                                              $q_immediateParent->where('keyword', $immediateParentKeywordName)
                                                                ->whereHas('parent', function ($q_grandparent) use ($grandparentKeywordName) {
                                                                    $q_grandparent->where('keyword', $grandparentKeywordName);
                                                                });
                                          })
                                          ->first();
                    }
                    // Add more elseif blocks here if you have deeper fixed paths (e.g., pathLength === 4)

                } else {
                    // This is a simple, unambiguous keyword entry (just the name string)
                    $keyword = Keyword::where('keyword', $keywordEntry)->first();
                }

                // Attach the found keyword to the study
                if ($keyword) {
                    $study->keywords()->attach($keyword->id);
                    $attachedKeywordCount++;
                } else {
                    $this->command->warn("Warning: Keyword path '" . (is_array($keywordEntry) ? implode(" > ", $keywordEntry) : $keywordEntry) . "' not found for study '" . $studyData['title'] . "'");
                }
            }
            $this->command->info("Seeded study: " . $study->title . " with " . $attachedKeywordCount . " keywords.");
        }
    }
}
