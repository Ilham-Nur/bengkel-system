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
        Schema::create('kwitansis', function (Blueprint $table): void {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->foreignId('work_order_id')->unique()->constrained('work_orders')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('customer_name');
            $table->string('customer_phone', 50)->nullable();
            $table->string('jenis_motor');
            $table->string('plat_nomor', 20);
            $table->decimal('total_kwitansi', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('kwitansi_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kwitansi_id')->constrained('kwitansis')->cascadeOnDelete();
            $table->text('item_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansi_items');
        Schema::dropIfExists('kwitansis');
    }
};
