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
        Schema::create('orders', function (Blueprint $table) {
            // 1. المعرف الأساسي بنظام الـ UUID
            $table->uuid('id')->primary();
            // 2. ربط الطلب بصاحبه (المستخدم)
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            // 3. كود فريد ومميز للطلب يظهر للعميل (زي ORD-2026-0001)
            $table->string('order_number')->unique();
            // 4. حالة الطلب، وبتبدأ بـ pending (قيد الانتظار)
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            // 5. الإجمالي الكلي للفلوس
            $table->decimal('total_price', 10, 2);
            // 6. بيانات التوصيل
            $table->string('shipping_address');
            $table->string('phone');
            $table->text('notes')->nullable(); // ملاحظات اختيارية
            // 7. بيانات الدفع
            $table->enum('payment_method', ['cash_on_delivery', 'card'])->default('cash_on_delivery');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
