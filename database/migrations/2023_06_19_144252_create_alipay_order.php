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
        Schema::create('alipay_order', function (Blueprint $table) {
            $table->id();
            $table->integer('created_at')->comment('创建时间');
            $table->integer('updated_at')->nullable()->comment('更新时间');
            $table->string('sn', 32)->comment('订单号');
            $table->string('remark', 255)->comment('备注');
            $table->decimal('price', 10, 2)->comment('价格');
            $table->string('alipay_sn', 255)->nullable()->comment('支付宝官方sn');
            $table->integer('pay_time')->nullable()->comment('支付时间');
            $table->tinyInteger('status')->default(0)->comment('状态:0=待支付,1=已支付');
            $table->unique('sn');
            $table->index('alipay_sn');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alipay_order');
    }
};
