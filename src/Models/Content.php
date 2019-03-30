<?php namespace Reflexions\Content\Models;

use Carbon\Carbon;

/**
 * Class Content
 * @package Reflexions\Content\Models
 *
 * @property File[] $files
 * @property string $model_type
 * @property string $model_id
 */
class Content extends \Eloquent {
    protected $attributes = [
        'publish_status' => Status::STUB,
    ];
    protected $table = 'content';
    protected $primaryKey = 'content_id';

    public static function fromModel(\Eloquent $instance)
    {
        $content = new static;
        $content->model_id = $instance->id;
        $content->model_type = $instance->getMorphClass();
        return $content;
    }

    public function getIdAttribute()
    {
        return $this->content_id;
    }

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        // overridden because laravel 5.4+ would otherwise return content_content_id
        return $this->primaryKey;
    }

    public function slugs()
    {
        return $this->hasMany(Slug::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tagged');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function fileGroups()
    {
        return $this->hasMany(FileGroup::class);
    }

    public function leadImage()
    {
        return $this->belongsTo(File::class, 'lead_image_id');
    }

    public function searchTokens()
    {
        return $this->belongsToMany(SearchToken::class, 'search_index', 'content_id', 'search_token_id');
    }

    public function postgresSearchIndex()
    {
        return $this->hasOne(PostgresSearchIndex::class);
    }

    public function setPublishStatusAttribute($value)
    {
        if ($value == Status::SCHEDULED) {
            $value = Status::PUBLISHED;
        }
        if ($value == Status::PUBLISHED && empty($this->publish_date)) {
            $this->publish_date = Carbon::now();
        }
        if ($value== Status::DRAFT) {
            $this->publish_date = null;
        }
        $this->attributes['publish_status'] = $value;
    }
    public function setPublishDateAttribute($value)
    {
        if ($this->publish_status == Status::DRAFT) {
            return;
        }
        $this->attributes['publish_date'] = $value;
    }

    /**
     * morph the provided classname
     */
    public static function morph($class_name)
    {
        if (class_exists($class_name)) {

            return (new $class_name())->getMorphClass();
        } else {
            // TODO: document when this is hit
            return $class_name;
        }
    }
}
