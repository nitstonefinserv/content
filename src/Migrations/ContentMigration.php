<?php namespace Reflexions\Content\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class ContentMigration extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('micro_content');
        Schema::dropIfExists('content');
        Schema::create('content', function ( Blueprint $table ) {
            $table->increments('content_id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('model_type');
            $table->integer('model_id')->unsigned();
            $table->string('publish_status', 64);
            $table->dateTime('publish_date')->nullable();
            $table->string('slug')->nullable();
            $table->integer('lead_image_id')->unsigned()->nullable();
            $table->timestamps();

            $table->unique(['model_type', 'slug']);
            $table->unique(['model_type', 'model_id']);
            $table->index(['model_type', 'publish_status', 'publish_date']);

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            // files table doesn't exist yet, so we'll add this fk later when creating the files table:
            // $table->foreign('lead_image_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('micro_content');
        Schema::dropIfExists('content');
    }

}
