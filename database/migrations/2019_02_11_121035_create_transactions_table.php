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
            $table->double('diskon')->default(0);
            $table->integer('status')->default(0);
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')
                    ->references('id')->on('branches')
                    ->onDelete('cascade');
            $table->unsignedInteger('cs_id');
            $table->foreign('cs_id')
                    ->references('id')->on('people');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')
                    ->references('id')->on('people');
            $table->unsignedInteger('cashier_id')->nullable();
            $table->foreign('cashier_id')
                    ->references('id')->on('people');
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
