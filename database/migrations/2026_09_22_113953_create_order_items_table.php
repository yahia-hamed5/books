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
        Schema::create('order_items', function (Blueprint $table) {
            // 1. المعرف الأساسي بنظام الـ UUID
            $table->uuid('id')->primary();
            // 2. ربط السطر ده بالفاتورة الرئيسية بتاعته
            $table->foreignUuid('order_id')->constrained('orders', 'id')->cascadeOnDelete();
            // 3. ربط السطر بالكتاب اللي اشتراه
            $table->foreignUuid('product_id')->constrained('products', 'id')->cascadeOnDelete();
            // 4. الكمية المطلوبة من الكتاب ده
            $table->unsignedInteger('quantity')->default(1);
            // 5. سعر النسخة الواحدة لحظة الشراء (تجميد السعر)
            $table->decimal('unit_price', 10, 2);
            // 6. الإجمالي الفرعي للسطر ده (سعر النسخة × الكمية)
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
