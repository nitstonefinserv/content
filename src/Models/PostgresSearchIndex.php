<?php

namespace Reflexions\Content\Models;

class PostgresSearchIndex extends \Eloquent {
    protected $fillable = ['text', 'content_id'];
    public $timestamps = false;
    protected $table = 'postgres_search_index';
    protected $primaryKey = 'postgres_search_index_id';

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
