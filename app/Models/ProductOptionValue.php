<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ProductOptionValue extends Model
{
    /**
     * items 컬렉션에서 option_values JSON의 모든 값 ID를 수집하고,
     * option 관계를 eager load하여 ID로 keyed된 컬렉션을 반환.
     */
    public static function resolveForItems($items): Collection
    {
        $valueIds = collect();

        foreach ($items as $item) {
            if ($item->option_values) {
                foreach ($item->option_values as $optId => $valId) {
                    $valueIds->push($valId);
                }
            }
        }

        $valueIds = $valueIds->unique()->filter();

        if ($valueIds->isEmpty()) {
            return collect();
        }

        return static::with('option')->whereIn('id', $valueIds)->get()->keyBy('id');
    }

    protected $fillable = [
        'product_option_id',
        'value',
        'price_modifier',
        'stock_quantity',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_modifier' => 'decimal:0',
            'stock_quantity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
