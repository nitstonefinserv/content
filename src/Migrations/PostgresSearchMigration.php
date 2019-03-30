<?php namespace Reflexions\Content\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use DB;

class PostgresSearchMigration extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('postgres_search_index');
        Schema::create('postgres_search_index', function(Blueprint $table)
        {
            $table->increments('postgres_search_index_id');
            $table->integer('content_id')->unsigned();
            $table->text('text');
        });
        DB::statement('ALTER TABLE postgres_search_index ADD COLUMN searchtext TSVECTOR;');

        DB::raw("
            CREATE INDEX postgres_search_index_searchtext_gin
            ON postgres_search_index
            USING gin(searchtext);");
        
        DB::statement("
            CREATE TRIGGER ts_postgres_search_index
            BEFORE INSERT OR UPDATE ON postgres_search_index
            FOR EACH ROW EXECUTE PROCEDURE
                tsvector_update_trigger(
                    'searchtext',
                    'pg_catalog.english',
                    'text'
                )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $conn = Schema::getConnection();

        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails('postgres_search_texts');
        if ($doctrineTable->hasIndex('postgres_search_index_searchtext_gin')) {
            Schema::dropIndex('postgres_search_index_searchtext_gin');
        }
        if ($doctrineTable->hasIndex('ts_postgres_search_index')) {
            Schema::dropIndex('ts_postgres_search_index');
        }

        Schema::dropIfExists('search_tokens');
        Schema::dropIfExists('search_index');
        Schema::dropIfExists('search_texts');
        Schema::dropIfExists('postgres_search_texts');
        Schema::dropIfExists('postgres_search_index');
    }

}