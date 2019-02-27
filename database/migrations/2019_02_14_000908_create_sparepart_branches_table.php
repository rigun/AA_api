<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSparepartBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sparepart_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sparepart_code');
            $table->foreign('sparepart_code')
                    ->references('code')->on('spareparts')
                    ->onDelete('cascade');
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')
                    ->references('id')->on('branches')
                    ->onDelete('cascade');
            $table->double('buy')->default(0);
            $table->double('sell')->default(0);
            $table->string('position');
            $table->integer('stock')->default(0);
            $table->integer('limitstock')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('sparepart_branches');
    }
}
