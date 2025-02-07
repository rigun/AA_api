<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->onDelete('cascade');
            $table->string('sparepart_code');
            $table->foreign('sparepart_code')
                  ->references('code')->on('spareparts')
                  ->onDelete('cascade');
            $table->string('unit');
            $table->integer('total');
            $table->integer('totalAccept')->default(0);
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
