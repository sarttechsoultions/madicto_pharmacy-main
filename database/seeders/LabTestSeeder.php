<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use Illuminate\Database\Seeder;

class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Labs
        $cityLab = Lab::firstOrCreate(
            ['name' => 'City Diagnostic Lab'],
            [
                'description' => 'Comprehensive multi-specialty pathology & diagnostics centre offering 100% accurate results with automated analyzers.',
                'contact_number' => '+91 98765 43210',
                'email' => 'contact@citydiagnosticlab.com',
                'address' => 'Shop 14, Health Plaza, M.G. Road',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'status' => 'active',
            ]
        );

        $abcLab = Lab::firstOrCreate(
            ['name' => 'ABC Laboratory'],
            [
                'description' => 'ISO & NABL accredited clinical reference laboratory with express report delivery and home sample collection.',
                'contact_number' => '+91 91234 56789',
                'email' => 'support@abclaboratory.in',
                'address' => 'Plot 88, BioTech Enclave, Sector 18',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
                'status' => 'active',
            ]
        );

        $apexLab = Lab::firstOrCreate(
            ['name' => 'Apex Healthcare Diagnostics'],
            [
                'description' => 'Advanced molecular pathology, cardiac screening and routine health checkup packages.',
                'contact_number' => '+91 99887 76655',
                'email' => 'care@apexdiagnostics.com',
                'address' => '22/A, Metro Station Lane, Indiranagar',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode' => '560038',
                'status' => 'active',
            ]
        );

        // 2. Seed Lab Test Categories
        $bloodCat = LabTestCategory::firstOrCreate(
            ['name' => 'Blood Test'],
            [
                'description' => 'Hematology & complete blood counts to evaluate overall health and detect disorders.',
                'status' => 'active',
            ]
        );

        $thyroidCat = LabTestCategory::firstOrCreate(
            ['name' => 'Thyroid Profile'],
            [
                'description' => 'Hormone evaluations including T3, T4 and TSH to evaluate thyroid gland function.',
                'status' => 'active',
            ]
        );

        $vitaminCat = LabTestCategory::firstOrCreate(
            ['name' => 'Vitamin Profile'],
            [
                'description' => 'Essential vitamins, Vitamin D3, B12 and minerals to support immunity and bone health.',
                'status' => 'active',
            ]
        );

        $lipidCat = LabTestCategory::firstOrCreate(
            ['name' => 'Lipid Profile'],
            [
                'description' => 'Cholesterol, HDL, LDL, VLDL and triglycerides assessment for cardiovascular health.',
                'status' => 'active',
            ]
        );

        $diabetesCat = LabTestCategory::firstOrCreate(
            ['name' => 'Diabetes Screening'],
            [
                'description' => 'Fasting blood sugar, Post Prandial (PP) and HbA1c glycated hemoglobin tests.',
                'status' => 'active',
            ]
        );

        // 3. Seed Lab Tests (from user prompt examples)
        $tests = [
            [
                'test_name' => 'CBC Test',
                'test_code' => 'CBC001',
                'lab_id' => $cityLab->id,
                'category_id' => $bloodCat->id,
                'description' => 'Complete Blood Count (CBC) measuring RBC, WBC, Platelets, Hemoglobin and Hematocrit.',
                'price' => 350.00,
                'discount_price' => 300.00,
                'preparation_instructions' => 'No special fasting required. Can be done at any time of the day.',
                'sample_type' => 'Blood',
                'report_delivery_time' => '24 Hours',
                'status' => 'active',
            ],
            [
                'test_name' => 'Thyroid Profile',
                'test_code' => 'THY001',
                'lab_id' => $cityLab->id,
                'category_id' => $thyroidCat->id,
                'description' => 'Comprehensive evaluation of T3, T4 and TSH hormones for hyperthyroidism or hypothyroidism.',
                'price' => 600.00,
                'discount_price' => 500.00,
                'preparation_instructions' => 'Fasting for 8-10 hours is recommended before sample collection.',
                'sample_type' => 'Blood',
                'report_delivery_time' => '24 Hours',
                'status' => 'active',
            ],
            [
                'test_name' => 'Vitamin D Test',
                'test_code' => 'VIT001',
                'lab_id' => $abcLab->id,
                'category_id' => $vitaminCat->id,
                'description' => 'Measures 25-hydroxyvitamin D levels to screen for deficiency and bone health risks.',
                'price' => 900.00,
                'discount_price' => 750.00,
                'preparation_instructions' => 'No special fasting required. Avoid biotin supplements 24 hours prior.',
                'sample_type' => 'Blood',
                'report_delivery_time' => '48 Hours',
                'status' => 'active',
            ],
            [
                'test_name' => 'Lipid Profile',
                'test_code' => 'LIP001',
                'lab_id' => $abcLab->id,
                'category_id' => $lipidCat->id,
                'description' => 'Complete cholesterol panel measuring Total Cholesterol, Triglycerides, HDL and LDL.',
                'price' => 500.00,
                'discount_price' => 450.00,
                'preparation_instructions' => 'Overnight fasting of 10-12 hours strictly required. Water is permitted.',
                'sample_type' => 'Blood',
                'report_delivery_time' => '24 Hours',
                'status' => 'active',
            ],
            [
                'test_name' => 'Blood Sugar',
                'test_code' => 'GLU001',
                'lab_id' => $cityLab->id,
                'category_id' => $diabetesCat->id,
                'description' => 'Fasting Blood Glucose Test to detect pre-diabetes, diabetes and monitor glucose control.',
                'price' => 150.00,
                'discount_price' => 120.00,
                'preparation_instructions' => '8-10 hours overnight fasting required before morning sample collection.',
                'sample_type' => 'Blood',
                'report_delivery_time' => '12 Hours',
                'status' => 'active',
            ],
            [
                'test_name' => 'HbA1c Glycated Hemoglobin',
                'test_code' => 'HBA001',
                'lab_id' => $apexLab->id,
                'category_id' => $diabetesCat->id,
                'description' => 'Evaluates average blood sugar control over the past 2 to 3 months.',
                'price' => 450.00,
                'discount_price' => 380.00,
                'preparation_instructions' => 'No fasting required. Can be done anytime.',
                'sample_type' => 'Blood',
                'report_delivery_time' => '24 Hours',
                'status' => 'active',
            ],
            [
                'test_name' => 'Urine Routine & Microscopic',
                'test_code' => 'URN001',
                'lab_id' => $apexLab->id,
                'category_id' => $bloodCat->id,
                'description' => 'Physical, chemical, and microscopic examination of urine sample for infection screening.',
                'price' => 200.00,
                'discount_price' => 180.00,
                'preparation_instructions' => 'First morning mid-stream clean catch urine specimen in sterile container.',
                'sample_type' => 'Urine',
                'report_delivery_time' => '12 Hours',
                'status' => 'active',
            ],
        ];

        foreach ($tests as $t) {
            LabTest::firstOrCreate(
                [
                    'test_code' => $t['test_code'],
                    'lab_id' => $t['lab_id'],
                ],
                $t
            );
        }
    }
}
