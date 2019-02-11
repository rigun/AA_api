<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactiondetailServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactiondetail_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trasanctiondetail_id');
            $table->foreign('trasanctiondetail_id')
                ->references('id')->on('transaction_details')
                ->onDelete('cascade');
            $table->unsignedInteger('service_id');
            $table->foreign('service_id')
                    ->references('id')->on('services')
                    ->onDelete('cascade');
            $table->integer('total');
            $table->double('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactiondetail_services');
    }
}
