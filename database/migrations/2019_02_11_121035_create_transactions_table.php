<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transactionNumber');
            $table->double('totalServices')->default(0);
            $table->double('totalSpareparts')->default(0);
            $table->double('totalCost')->default(0);
            $table->double('payment')->default(0);
            $table->unsignedInteger('discount_id')->nullable();
            $table->unsignedInteger('cs_id');
            $table->foreign('cs_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            $table->unsignedInteger('cashier_id');
            $table->foreign('cashier_id')
                    ->references('id')->on('users')
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
        Schema::dropIfExists('transactions');
    }
}
