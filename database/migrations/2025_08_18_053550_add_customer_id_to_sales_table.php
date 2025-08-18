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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('product_id')->constrained('customers')->onDelete('set null');
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
            $table->decimal('total_amount', 10, 2)->nullable()->after('unit_price');
            $table->string('sale_type', 50)->default('retail')->after('total_amount'); // retail, wholesale, etc.
            $table->text('notes')->nullable()->after('sale_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'unit_price', 'total_amount', 'sale_type', 'notes']);
        });
    }
};
