<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function serviceReport(): HasOne
    {
        return $this->hasOne(ServiceReport::class);
    }

    public function kwitansi(): HasOne
    {
        return $this->hasOne(Kwitansi::class);
    }

}

