<?php

namespace App\Imports;

use App\Models\CategoryModel;
use App\Models\medicineModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class MedicineImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use RemembersRowNumber;

    protected int $totalRows = 0;
    protected int $importedCount = 0;
    protected int $skippedCount = 0;
    protected int $failedCount = 0;
    protected int $totalValidationErrors = 0;

    /**
     * Store only the first 20 validation errors to avoid memory and session bloat.
     */
    protected array $validationErrors = [];

    protected array $categoriesById = [];
    protected array $categoriesByName = [];
    protected ?int $defaultCategoryId = null;

    public function __construct()
    {
        $this->loadCategories();
    }

    /**
     * Batch size for multi-row database inserts.
     */
    public function batchSize(): int
    {
        return 500;
    }

    /**
     * Chunk size for memory-efficient spreadsheet reading.
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Preload active categories once into memory for fast lookup.
     */
    protected function loadCategories(): void
    {
        try {
            $categories = CategoryModel::select('id', 'name')->get();
            foreach ($categories as $cat) {
                $id = (int)$cat->id;
                $this->categoriesById[$id] = $id;

                $normName = trim(mb_strtolower((string)$cat->name));
                if ($normName !== '') {
                    $this->categoriesByName[$normName] = $id;
                }
            }

            // Assign a logical default category if none specified (Prescriptions -> Wellness -> First available)
            $this->defaultCategoryId = $this->categoriesByName['prescriptions']
                ?? $this->categoriesByName['wellness']
                ?? (int)($categories->first()?->id ?? 1);
        } catch (\Throwable $e) {
            Log::warning('Could not preload categories in MedicineImport: ' . $e->getMessage());
        }
    }

    /**
     * Map each row to a new medicineModel instance or return null if skipped / invalid.
     */
    public function model(array $rawRow)
    {
        $this->totalRows++;
        $rowNum = $this->getRowNumber() ?? $this->totalRows;

        // 1. Normalize row keys and trim cell contents
        $normalized = $this->normalizeRow($rawRow);

        // 2. Identify and skip completely blank rows
        if (empty($normalized)) {
            $this->skippedCount++;
            return null;
        }

        // 3. Extract Medicine Name
        $name = $this->extractName($normalized);

        // 4. Skip secondary header rows that appear in the middle of data (e.g. multi-sheet sheets)
        if ($this->isHeaderRow($name)) {
            $this->skippedCount++;
            return null;
        }

        // 5. Extract Price (handles M.R.P., MRP, Price, Rate, numeric index 2)
        $priceRaw = $this->extractPrice($normalized);

        // 6. Validation checks for required fields
        $rowErrors = [];

        if (empty($name)) {
            $rowErrors[] = 'Medicine Name is required.';
        } elseif (mb_strlen($name) > 255) {
            $rowErrors[] = 'Medicine Name cannot exceed 255 characters.';
        }

        if ($priceRaw === null || $priceRaw === '') {
            $rowErrors[] = 'Price is required.';
        } elseif (!is_numeric($priceRaw)) {
            $rowErrors[] = 'Price must be a valid number.';
        } elseif ((float)$priceRaw < 0) {
            $rowErrors[] = 'Price cannot be negative.';
        }

        // 7. Resolve Category (with validation if category column is explicitly provided)
        $categoryResolution = $this->resolveCategory($normalized);
        if ($categoryResolution['error']) {
            $rowErrors[] = $categoryResolution['error'];
        }
        $categoryId = $categoryResolution['category_id'];

        // If there are validation failures, record them and DO NOT insert the medicine
        if (!empty($rowErrors)) {
            $this->failedCount++;
            $this->totalValidationErrors += count($rowErrors);

            // Keep only first 20 errors for display to prevent session/memory issues
            if (count($this->validationErrors) < 20) {
                $this->validationErrors[] = "Row {$rowNum}: " . implode(', ', $rowErrors);
            }

            return null;
        }

        // 8. Extract optional attributes
        $manufacturer = $this->extractField($normalized, [
            'company',
            'company_name',
            'manufacturer',
            'mfg',
            'brand',
            'make',
            'vendor',
            'producer',
            '3',
        ]);

        $unitType = $this->extractField($normalized, [
            'unit',
            'packing',
            'pack_size',
            'unit_type',
            'dosage_form',
            'package_size',
            '1',
        ]);

        $quantityRaw = $this->extractField($normalized, ['quantity', 'qty', 'stock_quantity']);
        $quantity = is_numeric($quantityRaw) ? (int)$quantityRaw : 100;

        $stockRaw = $this->extractField($normalized, ['stock', 'available_stock', 'current_stock']);
        $stock = is_numeric($stockRaw) ? (int)$stockRaw : $quantity;

        $discountRaw = $this->extractField($normalized, ['discount', 'discount_percent', 'discount_rate']);
        $discount = is_numeric($discountRaw) ? (float)$discountRaw : 0;

        $reorderRaw = $this->extractField($normalized, ['reorder_level', 'reorder_qty', 'min_stock']);
        $reorderLevel = is_numeric($reorderRaw) ? (int)$reorderRaw : 10;

        $statusRaw = $this->extractField($normalized, ['status', 'stock_status', 'availability']);
        $status = !empty($statusRaw) ? trim($statusRaw) : ($quantity > 5 ? 'In Stock' : ($quantity > 0 ? 'Low Stock' : 'Out of Stock'));

        $batchNo = $this->extractField($normalized, ['batch_no', 'batch_number', 'batch', 'lot_no']);
        $instructions = $this->extractField($normalized, ['usage_instructions', 'instructions', 'usage', 'directions']);
        $description = $this->extractField($normalized, ['description', 'desc', 'details']);

        $mfgDate = $this->parseDate($this->extractField($normalized, ['manufacture_date', 'mfg_date', 'mfgdate']));
        $expDate = $this->parseDate($this->extractField($normalized, ['expiry_date', 'exp_date', 'expdate']));

        $rawImage = $this->extractField($normalized, ['image', 'main_image', 'product_image', 'photo']);
        $rawGallery = $this->extractField($normalized, ['medicine_image', 'medicine_images', 'gallery_images', 'gallery']);
        $images = $this->parseImages($rawImage, $rawGallery);

        $this->importedCount++;

        return new medicineModel([
            'name' => $name,
            'category_id' => $categoryId,
            'batch_no' => $batchNo,
            'usage_instructions' => $instructions,
            'discount' => $discount,
            'quantity' => $quantity,
            'stock' => $stock,
            'manufacturer' => $manufacturer,
            'reorder_level' => $reorderLevel,
            'description' => $description,
            'price' => (float)$priceRaw,
            'unit_type' => $unitType,
            'pack_size' => $unitType,
            'status' => $status,
            'manufacture_date' => $mfgDate,
            'expiry_date' => $expDate,
            'image' => $images['image'],
            'medicine_image' => $images['medicine_image'],
            'dod' => 2,
        ]);
    }

    /**
     * Normalize row keys and filter out blank values.
     */
    protected function normalizeRow(array $rawRow): array
    {
        $normalized = [];

        foreach ($rawRow as $key => $value) {
            if ($value === null) {
                continue;
            }

            $cleanVal = is_string($value) ? trim($value) : $value;
            if ($cleanVal === '') {
                continue;
            }

            $cleanKey = trim(mb_strtolower((string)$key));
            $cleanKey = preg_replace('/[^a-z0-9]+/u', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');

            if ($cleanKey !== '') {
                $normalized[$cleanKey] = $cleanVal;
            }
        }

        return $normalized;
    }

    /**
     * Extract Medicine Name supporting all known header variants.
     */
    protected function extractName(array $normalized): ?string
    {
        $nameAliases = [
            'product_name',
            'iteams_name',
            'iteam_name',
            'item_name',
            'name',
            'medicine_name',
            'productname',
            'medicinename',
            'title',
            '0', // Index 0 for unheaded sheets
        ];

        foreach ($nameAliases as $alias) {
            if (isset($normalized[$alias]) && $normalized[$alias] !== '') {
                return (string)$normalized[$alias];
            }
        }

        // Fuzzy match: check if any key ends with 'name' or contains 'product_name'
        foreach ($normalized as $key => $val) {
            if (str_contains($key, 'product') || str_contains($key, 'medicine') || str_ends_with($key, '_name')) {
                if (!str_contains($key, 'company') && !str_contains($key, 'category')) {
                    return (string)$val;
                }
            }
        }

        return null;
    }

    /**
     * Detect if row content is a repeated header.
     */
    protected function isHeaderRow(?string $name): bool
    {
        if (empty($name)) {
            return false;
        }

        $headerTokens = [
            'product name',
            'iteams name',
            'iteam name',
            'item name',
            'name',
            'medicine name',
            'product_name',
            'medicine_name',
        ];

        return in_array(mb_strtolower(trim($name)), $headerTokens, true);
    }

    /**
     * Extract Price safely prioritizing M.R.P., MRP, Price, Rate.
     */
    protected function extractPrice(array $normalized)
    {
        // 1. Look for non-empty numeric price in exact alias priority
        $priceKeys = [
            'm_r_p',
            'mrp',
            'price',
            'unit_price',
            'rate',
            'selling_price',
            'cost',
            'm_r_p_',
            'mrp_',
            '2', // Index 2 for unheaded sheets
        ];

        foreach ($priceKeys as $key) {
            if (isset($normalized[$key]) && is_numeric($normalized[$key])) {
                return $normalized[$key];
            }
        }

        // 2. Fall back to any matching key even if string
        foreach ($priceKeys as $key) {
            if (isset($normalized[$key]) && $normalized[$key] !== '') {
                return $normalized[$key];
            }
        }

        return null;
    }

    /**
     * Resolve Category against database.
     */
    protected function resolveCategory(array $normalized): array
    {
        $categoryAliases = [
            'category_id',
            'category',
            'category_name',
            'cat_id',
            'categoryid',
            'categoryname',
        ];

        $categoryRaw = null;
        foreach ($categoryAliases as $alias) {
            if (isset($normalized[$alias]) && $normalized[$alias] !== '') {
                $categoryRaw = $normalized[$alias];
                break;
            }
        }

        // 1. Explicit Category column provided: Validate strictly against categories table
        if ($categoryRaw !== null) {
            // Check numeric ID
            if (is_numeric($categoryRaw)) {
                $intId = (int)$categoryRaw;
                if (isset($this->categoriesById[$intId])) {
                    return ['category_id' => $intId, 'error' => null];
                }
            }

            // Check exact category name
            $normCat = trim(mb_strtolower((string)$categoryRaw));
            if (isset($this->categoriesByName[$normCat])) {
                return ['category_id' => $this->categoriesByName[$normCat], 'error' => null];
            }

            // Check fuzzy / substring match
            foreach ($this->categoriesByName as $catName => $catId) {
                if ($catName === $normCat || str_contains($normCat, $catName) || str_contains($catName, $normCat)) {
                    return ['category_id' => $catId, 'error' => null];
                }
            }

            // Category was provided but could not be matched
            return [
                'category_id' => null,
                'error' => "Category '{$categoryRaw}' does not exist in the categories table.",
            ];
        }

        // 2. No Category column in Excel: check sheet-level headers or use default
        if (isset($normalized['general_iteams'])) {
            $catId = $this->categoriesByName['wellness'] ?? $this->defaultCategoryId;
            return ['category_id' => $catId, 'error' => null];
        }

        if (isset($normalized['ayurvedic_iteams'])) {
            $catId = $this->categoriesByName['wellness'] ?? $this->defaultCategoryId;
            return ['category_id' => $catId, 'error' => null];
        }

        if (isset($normalized['surgical_iteams'])) {
            $catId = $this->categoriesByName['prescriptions'] ?? $this->defaultCategoryId;
            return ['category_id' => $catId, 'error' => null];
        }

        // Default category for pharmacy inventory
        return [
            'category_id' => $this->defaultCategoryId,
            'error' => null,
        ];
    }

    /**
     * Helper to extract any field from an array of aliases.
     */
    protected function extractField(array $normalized, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            if (isset($normalized[$alias]) && $normalized[$alias] !== '') {
                return (string)$normalized[$alias];
            }
        }
        return null;
    }

    /**
     * Parse date safely.
     */
    protected function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && (float)$value > 10000 && (float)$value < 100000) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return is_string($value) ? trim($value) : null;
        }
    }

    /**
     * Parse main and gallery images.
     */
    protected function parseImages(?string $mainRaw, ?string $galleryRaw): array
    {
        $mainImage = null;
        if (!empty($mainRaw)) {
            $raw = trim($mainRaw);
            $parts = explode(',', $raw);
            $mainImage = trim($parts[0]);
        }

        $galleryImages = [];
        if (!empty($galleryRaw)) {
            $raw = trim($galleryRaw);
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $galleryImages = array_values(array_filter(array_map('trim', $decoded)));
            } else {
                $parts = explode(',', $raw);
                $galleryImages = array_values(array_filter(array_map('trim', $parts)));
            }
        }

        if (empty($mainImage) && !empty($galleryImages)) {
            $mainImage = $galleryImages[0];
        }

        return [
            'image' => $mainImage,
            'medicine_image' => json_encode($galleryImages),
        ];
    }

    // Getters for controller summary
    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }

    public function getTotalValidationErrors(): int
    {
        return $this->totalValidationErrors;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
