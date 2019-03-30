<?php namespace Reflexions\Content\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class FilesMigration extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('files');
        Schema::create(
            'files', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->string('filename');
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->string('link')->nullable();
            $table->string('group_name')->nullable();
            $table->string('group_slug')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->float('seq')->default(0);
            $table->timestamps();

            $table->index('content_id');
            $table->foreign('content_id')->references('content_id')->on('content');
        });

        // Add FK from content.lead_image_id => files.id
        Schema::table('content', function ( Blueprint $table )
        {
            $table->index('lead_image_id');
            $table->foreign('lead_image_id')->references('id')->on('files');
        });


        Schema::dropIfExists('file_groups');
        Schema::create(
            'file_groups', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->string('slug');

            $table->index('content_id');
            $table->foreign('content_id')->references('content_id')->on('content');
        }
        );

        Schema::dropIfExists('file_file_groups');
        Schema::create(
            'file_file_groups', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->integer('file_group_id')->unsigned();
            $table->integer('file_id')->unsigned();
            $table->float('seq')->default(0);

            $table->index('file_group_id');
            $table->foreign('file_group_id')->references('id')->on('file_groups');
            $table->index('file_id');
            $table->foreign('file_id')->references('id')->on('files');
        }
        );

        Schema::dropIfExists('thumbnails');
        Schema::create(
            'thumbnails', function ( Blueprint $table )
        {
            $table->increments('id');
            $table->integer('file_id')->unsigned();
            $table->string('name');
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            $table->index('file_id');
            $table->foreign('file_id')->references('id')->on('files');
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
        // Drop FK from content.lead_image_id => files.id
        Schema::table('content', function ( Blueprint $table )
        {
            $table->dropForeign('content_lead_image_id_foreign');
        });

        Schema::dropIfExists('file_file_groups');
        Schema::dropIfExists('file_groups');
        Schema::dropIfExists('thumbnails');
        Schema::dropIfExists('files');
    }

}
