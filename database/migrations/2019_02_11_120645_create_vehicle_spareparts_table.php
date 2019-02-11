<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleSparepartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_spareparts', function (Blueprint $table) {
            $table->unsignedInteger('vehicle_id');
            $table->foreign('vehicle_id')
                ->references('id')->on('vehicles')
                ->onDelete('cascade');
            $table->string('sparepart_code');
            $table->foreign('sparepart_code')
                    ->references('code')->on('spareparts')
                    ->onDelete('cascade');
            $table->primary(['vehicle_id','sparepart_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_spareparts');
    }
}
