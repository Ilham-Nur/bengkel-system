<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_order_id')->unique()->constrained('work_orders')->cascadeOnDelete();
            $table->dateTime('service_finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('service_report_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_report_id')->constrained('service_reports')->cascadeOnDelete();
            $table->foreignId('work_order_complaint_item_id')->constrained('work_order_complaint_items')->cascadeOnDelete();
            $table->text('service_description')->nullable();
            $table->timestamps();

            $table->unique(['service_report_id', 'work_order_complaint_item_id'], 'service_report_item_unique');
        });

        Schema::create('service_report_item_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_report_item_id')->constrained('service_report_items')->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('photo_description', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_report_item_photos');
        Schema::dropIfExists('service_report_items');
        Schema::dropIfExists('service_reports');
    }
};
