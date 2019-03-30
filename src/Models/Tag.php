<?php namespace Reflexions\Content\Models;

use Reflexions\Content\Traits\ContentTrait;
use Carbon\Carbon;
use Content as ContentFacade;

class Tag extends \Eloquent {
    use ContentTrait;


    protected $fillable = ['name', 'slug', 'term_group_name', 'term_group_slug', 'publish_status', 'publish_date'];
    public $timestamps = false;

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->slug = static::findUniqueSlug($value, Tag::class, $this->id);
    }

    public function setTermGroupAttribute($value)
    {
        $this->attributes['term_group'] = $value;
        $this->attributes['term_group_slug'] = str_slug($value);
    }

    public function tagged()
    {
        return $this->belongsToMany(Content::class, 'tagged');
    }

    public static function factory($name, $group='default')
    {
        $tag = Tag::findByNameAndGroup($name, $group);
        if (empty($tag)) {
            $tag = Tag::create([
                'name' => $name,
                'slug' => static::findUniqueSlug(str_slug($name), Tag::class),
                'term_group_name' => $group,
                'term_group_slug' => str_slug($group),
                'publish_status' => Status::PUBLISHED,
                'publish_date' => Carbon::now()
            ]);
        }
        return $tag;
    }

    public static function byGroup($group='default')
    {
        return Tag::with('content')
            ->where(function ($query) use ($group) {
                $query
                    ->where('term_group_name', $group)
                    ->orWhere('term_group_slug', str_slug($group));
            });
    }

    public static function findByNameAndGroup($name, $group='default')
    {
        return Tag::byGroup($group)
            ->where('name', $name)
            ->first();
    }

    public static function byPrefixAndGroup($prefix, $group='default')
    {
        return Tag::byGroup($group)
            ->join('content', function($join) use ($prefix) {
                $join
                    ->on('content.model_id', '=', 'tags.id')
                    ->where('content.model_type', '=', Content::morph(Tag::class))
                    ->where('content.slug', 'like', $prefix.'%');
            });
    }
}
