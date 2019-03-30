<?php namespace Reflexions\Content\Traits;

use Illuminate\Database\Eloquent\Builder;
use Reflexions\Content\Models\Tag;
use Reflexions\Content\Admin\Form\FieldSet;
use View;
use Content;
use Illuminate\Support\Facades\Input;

trait TaggableTrait
{
    /**
     * Eloquent query builder for tags
     */
    public function tags()
    {
        return $this->content->tags()->with('content');
    }

    /**
     * Return tags associated with model
     *
     * @return array $tags array of Reflexions\Content\ContentTrait\Tag
     */
    public function getTags($group='default')
    {
        return $this->tags()
            ->where(function ($query) use ($group) {
                $query
                    ->where('term_group_name', $group)
                    ->orWhere('term_group_slug', str_slug($group));
            })
            ->get();
    }

    /**
     * Return array of tags
     *
     * @return array $tags array in the format of [ 'tag-slug' => 'Tag Name' ]
     */
    public function getTagNames($group='default')
    {
        $names = [];
        foreach($this->getTags($group) as $t) {
            $names[$t->slug] = $t->name;
        }
        return $names;
    }
    
    /**
     * Return array of tags
     *
     * @return array $tags array in the format of [ 'tag-slug' => 'Tag Name' ]
     */
    public function getTagNamesAttribute($value)
    {
        return $this->getTagNames();
    }

    /**
     * @param $tagArray
     * @return \Illuminate\Support\Collection
     */
    private function _prepareTagArray($tagArray)
    {
        if (!is_array($tagArray)) { $tagArray = array($tagArray); }

        return collect($tagArray)
            ->map('trim')
            ->map('ucwords')
            ->filter();
    }

    /**
     * Add a tag to the model
     *
     * @param string|array $tag single or multiple tags
     */
    public function addTag($tagArray, $group='default')
    {
        $tagArray = $this->_prepareTagArray($tagArray);

        foreach($tagArray as $name) {
            $tag = Tag::factory($name, $group);
            if ($this->tags()->where('tags.id', $tag->id)->count() > 0) {
                continue;
            } else {
                $this->tags()->attach($tag->id);
            }
        }
    }

    /**
     * Remove a tag to the model
     *
     * @param string|array $tag single or multiple tags
     */
    public function removeTag($tagArray, $group='default')
    {
        $tagArray = $this->_prepareTagArray($tagArray);
        foreach($tagArray as $name) {
            $tag = Tag::findByNameAndGroup($name, $group);
            if (empty($tag)) continue;
            $this->tags()->detach($tag->id);
        }

    }

    /**
     * Replace the tags from this model
     *
     * @param $tagName string or array
     */
    public function replaceTags($tagArray, $group='default')
    {
        $tagArray = $this->_prepareTagArray($tagArray);

        $ids = $tagArray->map(function($name) use ($group) {
            $tag = Tag::factory($name, $group);
            return $tag->id;
        })->toArray();

        $this->tags()->detach(Tag::byGroup($group)->pluck('id')->toArray());
        $this->tags()->attach($ids);
    }

    /**
     * Provides a tags admin field for use in Reflexions\Content\Admin
     */
    public function getTaggableAdminField($group='default')
    {
        $model = $this;
        $group_slug = str_slug($group);
        return new FieldSet(
            [],
            [],
            [View::make(Content::package().'::contenttrait.admin.taggable', compact('group', 'group_slug', 'model'))],
            [],
            [$group_slug => function ($group_slug, $model) use ($group) {
                $tags = explode(',', Input::get($group_slug));
                $model->replaceTags($tags, $group);
            }]
        );
    }

    /**
     * Provides query scope for limiting by tag.  Accepts string or array arguments.
     * All tags per call are joined with 'OR' logic.
     * For 'AND' logic, call query scope multiple times.
     *
     * @deprecated prefer scopeOfTagSlugs: lookup by slug, not by name
     */
    public function scopeOfTags(Builder $query, $tags)
    {
        if (!is_array($tags)) { $tags = [$tags]; }
        $tags = $this->_prepareTagArray($tags);

        $query = $query->withContent();

        $alias = 1;
        foreach($query->getQuery()->joins as $j) {
            if (preg_match('#^tagged(_\d+)?$#', $j->table)) {
                $alias++;
            }
        }
        $alias = "_$alias";

        $query = $query->whereIn('content.content_id', function ($query) use ($tags, $alias) {
            $query->select("tagged{$alias}.content_id")
                ->from("tagged as tagged{$alias}")
                ->join("tags as tags{$alias}", "tags{$alias}.id", '=', "tagged{$alias}.tag_id")
                ->whereIn("tags{$alias}.name", $tags);
        });

        return $query;
    }

    /**
     * Provides query scope for limiting by tag.  Accepts string or array arguments.
     * All tags per call are joined with 'OR' logic.
     * For 'AND' logic, call query scope multiple times.
     */
    public function scopeOfTagSlugs(Builder $query, $tag_slugs)
    {
        if (!is_array($tag_slugs)) { $tag_slugs = [$tag_slugs]; }

        $query = $query->withContent();

        $alias = 1;
        foreach($query->getQuery()->joins as $j) {
            if (preg_match('#^tagged(_\d+)?$#', $j->table)) {
                $alias++;
            }
        }
        $alias = "_$alias";

        $query = $query->whereIn('content.content_id', function ($query) use ($tag_slugs, $alias) {
            $query->select("tagged{$alias}.content_id")
                ->from("tagged as tagged{$alias}")
                ->join("tags as tags{$alias}", "tags{$alias}.id", "=", "tagged{$alias}.tag_id")
                ->whereIn("tags{$alias}.slug", $tag_slugs);
        });

        return $query;
    }
}
