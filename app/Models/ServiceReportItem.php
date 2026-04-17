<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'service_report_id',
    'work_order_complaint_item_id',
    'service_description',
])]
class ServiceReportItem extends Model
{
    public function report(): BelongsTo
    {
        return $this->belongsTo(ServiceReport::class, 'service_report_id');
    }

    public function complaintItem(): BelongsTo
    {
        return $this->belongsTo(WorkOrderComplaintItem::class, 'work_order_complaint_item_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ServiceReportItemPhoto::class);
    }
}
