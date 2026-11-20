<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medicineModel extends Model
{
    protected $table = 'medicine';

    protected $fillable = [
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
        'medicine_image',
        'dod',
    ];

    protected $appends = [
        'image_url',
        'gallery_images',
        'gallery_image_urls',
        'final_price',
        'is_in_stock',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(OrdersModel::class, 'medicine_id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewModel::class, 'medicine_id', 'id');
    }

    /**
     * Scope query to active medicines only (excludes Inactive).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', '!=', 'Inactive');
        });
    }

    /**
     * Scope query for comprehensive search across name, brand/manufacturer, composition/description.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        $term = trim($search);

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('manufacturer', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('usage_instructions', 'LIKE', "%{$term}%")
              ->orWhere('unit_type', 'LIKE', "%{$term}%")
              ->orWhere('pack_size', 'LIKE', "%{$term}%")
              ->orWhere('batch_no', 'LIKE', "%{$term}%")
              ->orWhereHas('category', function ($cq) use ($term) {
                  $cq->where('name', 'LIKE', "%{$term}%");
              });
        });
    }

    /**
     * Accessor for full URL of the main image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'uploads/')) {
            return asset($this->image);
        }

        return asset('uploads/medicine/' . $this->image);
    }

    /**
     * Safely access gallery images as an array of paths/filenames.
     */
    public function getGalleryImagesAttribute(): array
    {
        if (is_array($this->medicine_image)) {
            return array_values(array_filter($this->medicine_image));
        }

        if (empty($this->medicine_image)) {
            return [];
        }

        $decoded = json_decode($this->medicine_image, true);
        if (is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return [$this->medicine_image];
    }

    /**
     * Accessor for full URLs of gallery images.
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        $images = $this->gallery_images;
        $urls = [];

        foreach ($images as $img) {
            if (empty($img)) {
                continue;
            }

            if (filter_var($img, FILTER_VALIDATE_URL)) {
                $urls[] = $img;
            } elseif (str_starts_with($img, 'uploads/')) {
                $urls[] = asset($img);
            } else {
                $urls[] = asset('uploads/medicine/gallery/' . $img);
            }
        }

        return $urls;
    }

    /**
     * Accessor for calculated final price after discount.
     */
    public function getFinalPriceAttribute(): float
    {
        $price = (float)($this->price ?? 0);
        $discount = (float)($this->discount ?? 0);

        if ($discount > 0 && $price > 0) {
            return round($price - (($price * $discount) / 100), 2);
        }

        return round($price, 2);
    }

    /**
     * Accessor for whether the item is active and has stock.
     */
    public function getIsInStockAttribute(): bool
    {
        if ($this->status === 'Inactive') {
            return false;
        }

        return ((int)($this->quantity ?? $this->stock ?? 0)) > 0;
    }
}
