<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('no_wo')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_motor');
            $table->string('plat_nomor', 20);
            $table->unsignedInteger('km_motor');
            $table->decimal('total_keluhan_biaya', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('work_order_complaint_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->text('keluhan_item');
            $table->text('rekomendasi_perbaikan')->nullable();
            $table->text('sparepart')->nullable();
            $table->decimal('estimasi_biaya', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('work_order_complaint_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('work_order_complaint_item_id')
                ->constrained('work_order_complaint_items')
                ->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('photo_description', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_complaint_photos');
        Schema::dropIfExists('work_order_complaint_items');
        Schema::dropIfExists('work_orders');
    }
};
