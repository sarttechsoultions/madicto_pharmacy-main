<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class MedicineSampleExport implements FromArray
{
    public function array(): array
    {
        return [
            [
                'name',
                'category_id',
                'batch_no',
                'usage_instructions',
                'discount',
                'quantity',
                'stock',
                'manufacturer',
                'reorder_level',
                'description',
                'price',
                'unit_type',
                'pack_size',
                'status',
                'manufacture_date',
                'expiry_date',
                'image',
                'medicine_images'
            ],
            [
                'Paracetamol 500mg',
                9,
                'B123',
                'After meal twice daily',
                10,
                100,
                100,
                'ABC Pharma',
                20,
                'Fever and headache relief',
                50,
                'Tablets',
                '10 Tablets',
                'In Stock',
                '2026-01-01',
                '2028-01-01',
                'uploads/medicine/paracetamol.jpg',
                'uploads/medicine/gallery/g1.jpg, uploads/medicine/gallery/g2.jpg'
            ],
            [
                'Vitamin C 1000mg',
                5,
                'B124',
                'One tablet daily with water',
                5,
                80,
                80,
                'HealthCare Ltd',
                15,
                'Immunity booster and antioxidant supplement',
                35,
                'Tablets',
                '30 Tablets',
                'In Stock',
                '2026-02-01',
                '2028-02-01',
                'uploads/medicine/vitaminc.jpg',
                'uploads/medicine/gallery/v1.jpg'
            ],
        ];
    }
}
