<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactiondetailSparepartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactiondetail_spareparts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trasanctiondetail_id');
            $table->foreign('trasanctiondetail_id')
                ->references('id')->on('transaction_details')
                ->onDelete('cascade');
            $table->string('sparepart_code');
            $table->foreign('sparepart_code')
                    ->references('code')->on('spareparts')
                    ->onDelete('cascade');
            $table->integer('total');
            $table->double('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactiondetail_spareparts');
    }
}
