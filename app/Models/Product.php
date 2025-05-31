<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'full_description',
        'composition',
        'image',
        'price',
        'termin',
        'quantity',
        'code',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'composition' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    /**
     * Get composition as formatted string
     */
    public function getCompositionStringAttribute()
    {
        if (!$this->composition) {
            return 'Склад не вказано';
        }

        $parts = [];
        foreach ($this->composition as $ingredient => $percentage) {
            $parts[] = ucfirst($ingredient) . ': ' . $percentage . '%';
        }

        return implode(', ', $parts);
    }

    /**
     * Get active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get products with low stock
     */
    public function scopeLowStock($query, $threshold = 5)
    {
        return $query->where('quantity', '<=', $threshold);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')->withPivot('amount');
    }
}