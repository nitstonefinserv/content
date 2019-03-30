<?php namespace Reflexions\Content\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class TagsMigration extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tags');
        Schema::create(
            'tags', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->string('name');
            $table->string('term_group_name');
            $table->string('term_group_slug');
            $table->index(
                [
                    'term_group_slug',
                    'name',
                ]
            );
        }
        );
        Schema::dropIfExists('tagged');
        Schema::create(
            'tagged', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->integer('tag_id')->unsigned();

            // postgres doesn't automatically create indexes for foreign keys
            $table->index('content_id');
            $table->index('tag_id');

            $table->foreign('content_id')->references('content_id')->on('content');
            $table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::dropIfExists('tagged');
        Schema::dropIfExists('tags');
    }

}
