<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_suppliers', function (Blueprint $table) {
            $table->unsignedInteger('sales_id');
            $table->foreign('sales_id')
                    ->references('id')->on('people')
                    ->onDelete('cascade');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                    ->references('id')->on('orders')
                    ->onDelete('cascade');
            $table->primary(['sales_id','order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_suppliers');
    }
}
