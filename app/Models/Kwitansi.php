<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'no_invoice',
    'work_order_id',
    'tanggal',
    'customer_name',
    'customer_phone',
    'jenis_motor',
    'plat_nomor',
    'total_kwitansi',
    'is_paid',
    'paid_at',
])]
class Kwitansi extends Model
{
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(KwitansiItem::class);
    }

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }
}

