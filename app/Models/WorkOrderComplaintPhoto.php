<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'work_order_complaint_item_id',
    'photo_path',
    'photo_description',
])]
class WorkOrderComplaintPhoto extends Model
{
    public function complaintItem(): BelongsTo
    {
        return $this->belongsTo(WorkOrderComplaintItem::class, 'work_order_complaint_item_id');
    }
}
