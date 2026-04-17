<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_report_item_id',
    'photo_path',
    'photo_description',
])]
class ServiceReportItemPhoto extends Model
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(ServiceReportItem::class, 'service_report_item_id');
    }
}
