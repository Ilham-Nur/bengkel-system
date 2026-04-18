<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'kwitansi_id',
    'item_name',
    'qty',
    'unit_price',
    'subtotal',
])]
class KwitansiItem extends Model
{
    public function kwitansi(): BelongsTo
    {
        return $this->belongsTo(Kwitansi::class);
    }
}
