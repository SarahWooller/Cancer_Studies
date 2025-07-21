<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Keyword::truncate();

        $keywordsData = [
            [
                "Primary site" => [
                    "Primary sites" => [
                        "Breast",
                        "Central Nervous System",
                        "Colorectal",
                        "Gynaecological",
                        "Haematological",
                        "Head and Neck",
                        "Liver",
                        "Lung",
                        "Sarcoma",
                        "Skin",
                        "Unknown",
                        "Upper GI",
                        "Urological"
                    ]
                ]
            ],
            [
                "In Vitro Study" => [
                    "Cell Line Study" => [
                        "Cell Line Type" => [
                            "Adherent",
                            "Suspension"
                        ],
                        "Cell Source" => [
                            "Human",
                            "Mouse"
                        ],
                        "Genetic Modification" => [
                            "Edited",
                            "Wild-type"
                        ],
                    ],
                    "Organ-on-a-Chip Study" => [
                        "Cell Source" => [
                            "Cell line",
                            "Mouse",
                            "Patient",
                            "induced Pluripotent Stem Cell"
                        ],
                        "OOAC Platform/Type" => [
                            "Multi-organ",
                            "Organ",
                            "Tumour"
                        ]
                    ],
                    "Organoid Study" => [
                        "Organoid Source" => [
                            "Cell line",
                            "Induced Pluripotent Stem Cell",
                            "Mouse",
                            "Patient"
                        ],
                    ]
                ]
            ],
            [
                "Mouse study" => [
                    "Biobank Samples" => [
                        "Bloods",
                        "Cells",
                        "DNA/RNA",
                        "Other Fluids",
                        "Organoids",
                        "Tissues"
                    ],
                    "Biopsy & Lab Results" => [
                        "Biomarkers",
                        "Flow Cytometry",
                        "Immunohistochemistry"
                    ],
                    "Imaging Data" => [
                        "Medical photography",
                        "Microscopy",
                        "Magnetic resonance imaging",
                        "Nuclear medicine imaging procedure",
                        "Radiographic imaging procedure",
                        "Ultrasonography",
                        "Fluorescence imaging",
                        "Bioluminescence Imaging",
                    ],
                    "Longitudinal Follow up" => [
                        "Behavioural data",
                        "Clinical observations",
                        "Response outcomes",
                        "Side effects",
                        "Survival data"
                    ],
                    "Multi-omic Data" => [
                        "Circulating tumour cells",
                        "Circulating tumour DNA",
                        "Copy Number Variations",
                        "Epigenetic Data",
                        "Exosomes/Genomes",
                        "Fusion Genes",
                        "Germline Mutations",
                        "Metabolomics",
                        "Protein expression profiles",
                        "RNA Sequence Expression Profile",
                        "Single-cell",
                        "Somatic Mutations",
                        "Spatial Biology Data"
                    ],
                    "Treatments" => [
                        "Medication",
                        "Organ resection and other ablations",
                        "Radiotherapies"
                    ],
                    "Tumour Model" => [
                        "Genetically engineered mouse model",
                        "Patient-Derived xenograft",
                        "Syngeneic"
                    ]
                ]
            ],
            [
                "Patient study" => [
                    "Background" => [
                        "Demographic",
                        "Family history",
                        "Lifestyle",
                        "Quality of life (eg Education and/or employment)"
                    ],
                    "Biobank Samples" => [
                        "Bloods",
                        "Cells",
                        "DNA/RNA",
                        "Other Fluids",
                        "Organoids",
                        "Primary cell lines",
                        "Tissues"
                    ],
                    "Biopsy Reports and Lab Results" => [
                        "Biomarkers",
                        "Complete blood count",
                        "H&E-stained tissue microarrays",
                        "Immunohistochemistry",
                        "Kidney function tests",
                        "Liver function tests",
                        "Other bodily fluid analyses",
                        "Tumour details",
                        "Urine tests"
                    ],
                    "Imaging Data" => [
                        "Bone Scans",
                        "Computed Tomography",
                        "Imaging Mass Cytometry",
                        "Magnetic Resonance Imaging",
                        "Mammography",
                        "Positron Emission Tomography",
                        "Tomosynthesis",
                        "Ultrasound",
                        "X-rays"
                    ],
                    "Longitudinal Follow up" => [
                        "Patient-Reported Outcomes",
                        "Response outcomes",
                        "Side effects",
                        "Survival data"
                    ],
                    "Multi-omic Data" => [
                        "Circulating tumour cells",
                        "Circulating tumour DNA",
                        "Copy Number Variations",
                        "Epigenetic Data",
                        "Exosomes/Genomes",
                        "Fusion Genes",
                        "Germline Mutations",
                        "Metabolomics",
                        "Protein expression profiles",
                        "RNA Sequence Expression Profile",
                        "Single-cell",
                        "Somatic Mutations",
                        "Spatial Biology Data"
                    ],
                    "Treatments" => [
                        "Medication",
                        "Organ resection and other ablations",
                        "Radiotherapies"
                    ]
                ]
            ],
            [
                "Population Study" => [
                    "Data Sources" => [
                        "Administrative data (e.g., insurance claims)",
                        "Biobanks (population cohorts)",
                        "Cancer registries",
                        "Electronic Health Records (EHR)",
                        "Environmental monitoring data",
                        "National health surveys"
                    ],
                    "Interventions/Policies" => [
                        "Health education campaigns",
                        "Policy changes",
                        "Prevention programs",
                        "Screening programs"
                    ],
                    "Outcomes Measured" => [
                        "Education/Employment",
                        "Incidence",
                        "Morbidity/Mortality",
                        "Social well-being"
                    ],
                    "Population Demographics" => [
                        "Age",
                        "Ethnicity",
                        "Geographic location",
                        "Sex",
                        "Socioeconomic Status"
                    ],
                    "Risk Factors" => [
                        "Environmental",
                        "Genetic",
                        "Infections",
                        "Lifestyle",
                        "Medical history",
                        "Occupational"
                    ],
                    "Study Design" => [
                        "Case-control study",
                        "Cohort study",
                        "Cross-sectional study",
                        "Ecological study",
                        "Intervention study"
                    ]
                ]
            ]
        ];

        foreach ($keywordsData as $topLevelArray) {
            foreach ($topLevelArray as $categoryName => $categoryData) {
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
