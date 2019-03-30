<?php namespace Reflexions\Content\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Reflexions\Content\Models\PostgresSearchIndex;

/**
 * UNTESTED
 * Would need to figure out how to mock a postgres db for testing => use docker-compose.testing.yml
 *
 * TODO: use Laravel Scout instead of this custom class
 */
trait PostgresSearchableTrait {

    /**
     * Hook into laravel magic boot method to register observers
     *
     * @return void
     */
    public static function bootPostgresSearchableTrait()
    {
        static::saved(function($model)
        {
            $text = static::getModelSearchDocument($model);
            $model->index($text);
        });
        static::deleting(function($model)
        {
            $model->unindex();
        });
    }

    public static function getPostgresSearchableTraitTable()
    {
        return 'postgres_search_index';
    }

    public static function getModelSearchDocument($model)
    {
        // Note: this is often overridden in the model when html_entity_decode or transliteration is needed

        return collect($model->getFillable())
            ->map(function($attribute) use ($model) {
                return (string) $model->$attribute;
            })
            ->implode("\n\n");
    }

    /**
     * Index this instance using the provided search document.
     * A search document is a key => value dictionary
     * where both the key and values are strings
     */
    public function index($text)
    {
        if (!$this->exists) {
            throw new Exception("Model must be saved prior to indexing");
        }
        PostgresSearchIndex::updateOrCreate(
            ['content_id' => $this->content->content_id],
            ['content_id' => $this->content->content_id, 'text' => $text]
        );
    }

    /**
     * Search using the provided text
     */
    public function scopePgSearch(Builder $query, $text)
    {
        $query = $query->withContent();

        $table = static::getPostgresSearchableTraitTable();

        $alias = 1;
        foreach($query->getQuery()->joins as $j) {
            if (preg_match('#^' . preg_quote($table, '#') . '(_\d+)?$#', $j->table)) {
                $alias++;
            }
        }
        $alias = "_$alias";

        $query = $query
            ->join($table . ' as psi'.$alias, 'psi'.$alias.'.content_id', '=', 'content.content_id')
            ->whereRaw("psi".$alias.".searchtext @@ plainto_tsquery('english', ?)", [$text]);
            
        return $query;
    }

    /**
     * Return highlighted excerpt from search query
     *
     * @param $query string The search terms
     * @param $limit int The number of highlights to show
     * @param $max_words int See https://www.postgresql.org/docs/9.6/static/textsearch-controls.html#TEXTSEARCH-HEADLINE
     * @param $min_words int Same docs as $max_words
     *
     * @return string|null Null if we couldn't search, empty string if no results.
     */
    public function searchExcerpt($query, $limit = 5, $max_words = 35, $min_words = 15)
    {
        $search_record = $this->content->postgresSearchIndex()->first();
        if (!$search_record) return null; // didn't find a record
        $text = $search_record->text;
        if (!$text) return null; // record didn't have text

        $stripped_text = htmlspecialchars(strip_tags($text));

        $highlights = DB::select(
                "
                    SELECT ts_headline(
                        'english',
                        ?,
                         plainto_tsquery('english', ?),
                        'StartSel = \"<span class=''highlight''>\", StopSel = \"</span>\", MaxWords = $max_words, MinWords = $min_words, MaxFragments = $limit'
                    ) as highlight
                ",
                [
                    $stripped_text,
                    $query
                ]
            );

        return $highlights ? $highlights[0]->highlight : '';
    }

    /* -----------------------------------------------------
        Yanked code below
       ----------------------------------------------------- */

    /**
     * Remove this object from the index
     */
    public function unindex()
    {
        $table = static::getPostgresSearchableTraitTable();
        DB::delete("delete from $table where content_id = ?", [$this->content->content_id]);
    }

}
