<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'work_order_id',
    'keluhan_item',
    'rekomendasi_perbaikan',
    'sparepart',
    'estimasi_biaya',
])]
class WorkOrderComplaintItem extends Model
{
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(WorkOrderComplaintPhoto::class, 'work_order_complaint_item_id');
    }
}
