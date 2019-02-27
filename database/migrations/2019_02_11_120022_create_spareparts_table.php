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
            $table->string('merk');
            $table->string('type');
            $table->string('picture');
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')
                  ->references('id')->on('people')
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
