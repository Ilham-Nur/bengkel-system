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
        Schema::table('kwitansis', function (Blueprint $table): void {
            $table->boolean('is_paid')->default(false)->after('total_kwitansi');
            $table->timestamp('paid_at')->nullable()->after('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kwitansis', function (Blueprint $table): void {
            $table->dropColumn(['is_paid', 'paid_at']);
        });
    }
};
