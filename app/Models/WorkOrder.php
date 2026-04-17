<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'no_wo',
    'user_id',
    'tanggal',
    'jenis_motor',
    'plat_nomor',
    'km_motor',
    'total_keluhan_biaya',
])]
class WorkOrder extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function complaintItems(): HasMany
    {
        return $this->hasMany(WorkOrderComplaintItem::class);
    }
}
