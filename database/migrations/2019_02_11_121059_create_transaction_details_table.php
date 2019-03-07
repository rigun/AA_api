<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')
                    ->references('id')->on('transactions')
                    ->onDelete('cascade');
            $table->unsignedInteger('vehicleCustomer_id');
            $table->foreign('vehicleCustomer_id')
                    ->references('id')->on('vehicle_customers');            
            $table->unsignedInteger('montir_id')->nullable();
            $table->foreign('montir_id')
                    ->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_details');
    }
}
