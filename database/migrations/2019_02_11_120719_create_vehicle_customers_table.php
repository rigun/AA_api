<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('licensePlate');
            $table->unsignedInteger('vehicle_id');
            $table->foreign('vehicle_id')
                ->references('id')->on('vehicles')
                ->onDelete('cascade');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')
                    ->references('id')->on('people')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('vehicle_customers');
    }
}
