<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Reflexions\Content\Traits\Publishable;

class CreateTestbench extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Content::migrate();

        Schema::dropIfExists('testbench');
        Schema::create('testbench', function(Blueprint $table)
        {
            $table->increments('id');
            $table->text('text')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('anothertestbench');
        Schema::create('anothertestbench', function(Blueprint $table)
        {
            $table->increments('id');
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
        Schema::dropIfExists('testbench');
        Schema::dropIfExists('anothertestbench');
        Content::rollback();
    }
}
