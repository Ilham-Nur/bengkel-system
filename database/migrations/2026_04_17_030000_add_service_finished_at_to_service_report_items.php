<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_report_items', function (Blueprint $table): void {
            $table->dateTime('service_finished_at')->nullable()->after('work_order_complaint_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_report_items', function (Blueprint $table): void {
            $table->dropColumn('service_finished_at');
        });
    }
};
