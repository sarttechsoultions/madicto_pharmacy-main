<?php

namespace App\Imports;

use App\Models\medicineModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicineImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new medicineModel([
            'name' => $row['name'],
            'category_id' => $row['category_id'],
            'batch_no' => $row['batch_no'],
            'usage_instructions' => $row['usage_instructions'],
            'discount' => $row['discount'],
            'quantity' => $row['quantity'],
            'stock' => $row['stock'],
            'manufacturer' => $row['manufacturer'],
            'reorder_level' => $row['reorder_level'],
            'description' => $row['description'],
            'price' => $row['price'],
            'unit_type' => $row['unit_type'],
            'pack_size' => $row['pack_size'],
            'status' => $row['status'],
            'manufacture_date' => $row['manufacture_date'],
            'expiry_date' => $row['expiry_date'],
            // Direct path save
            'image' => $row['image'] ?? null,

            // Multiple image paths
            'medicine_image' => !empty($row['medicine_images'])
                ? json_encode(array_map('trim', explode(',', $row['medicine_images'])))
                : json_encode([]),

        ]);
    }
}
