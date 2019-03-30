<?php namespace Reflexions\Content\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class SlugsMigration extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('slugs');
        Schema::create(
            'slugs', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->string('slug', 255)->index();
            $table->timestamps();

            $table->index('content_id');
            $table->foreign('content_id')->references('content_id')->on('content');
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slugs');
    }

}
