<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSparepartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spareparts', function (Blueprint $table) {
            $table->string('code')->unique();
            $table->string('name');
            $table->double('buy')->default(0);
            $table->double('sell')->default(0);
            $table->string('merk');
            $table->string('type');
            $table->string('position');
            $table->integer('stock')->default(0);
            $table->integer('limitstock')->default(0);
            $table->integer('status')->default(0);
            $table->unsignedInteger('people_id');
            $table->foreign('people_id')
                  ->references('id')->on('people')
                  ->onDelete('cascade');
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')
                    ->references('id')->on('branches')
                    ->onDelete('cascade');
            $table->timestamps();
            $table->primary('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spareparts');
    }
}
