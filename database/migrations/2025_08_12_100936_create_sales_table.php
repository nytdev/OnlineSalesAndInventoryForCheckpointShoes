<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sales_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->timestamp('date');
            $table->foreignId('customer_id')->nullable()->after('product_id')->constrained('customers')->onDelete('set null');
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
            $table->decimal('total_amount', 10, 2)->nullable()->after('unit_price');
            $table->string('sale_type', 50)->default('retail'); // retail, wholesale, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
