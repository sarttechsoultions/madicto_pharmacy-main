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
                'Paracetamol',
                1,
                'B123',
                'After meal',
                10,
                100,
                500,
                'ABC Pharma',
                20,
                'Fever medicine',
                50,
                'Tablet',
                '10',
                'In Stock',
                '2026-01-01',
                '2028-01-01',
                'uploads/medicine/paracetamol.jpg',
                'uploads/medicine/gallery/g1.jpg,uploads/medicine/gallery/g2.jpg'
            ],
        ];
    }
}
