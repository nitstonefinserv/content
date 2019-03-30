<?php namespace Reflexions\Content\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Reflexions\Content\Models\Content;
use Reflexions\Content\Models\Slug;
use Validator;
use Auth;

/**
 * Class ContentTrait
 * @package Reflexions\Content\Traits
 *
 * @property Content $content
 */
trait ContentTrait
{
    /**
     * Hook into laravel magic boot method to register observers
     *
     * @return void
     */
    public static function bootContentTrait()
    {
        static::saved(
            function ( $model )
            {
                $model->content->model_type = $model->getMorphClass();
                $model->content->model_id = $model->id;
                if( Auth::user() )
                {
                    $model->content->user_id = Auth::user()->id;
                }
                $model->content->save();
            }
        );

        Validator::extend(
            'unique_slug', function ( $attribute, $value, $parameters, $validator )
        {
            if( empty($parameters) )
            {
                return true;
            }
            $parameters = array_filter($parameters);
            if( 2 != count($parameters) )
            {
                return true;
            }

            $model_type = $parameters[0];
            $model_id = $parameters[1];

            $existing_slugs = Slug::existing_slugs($model_type, $model_id)
                ->where('slug', $value);

            return 0 == $existing_slugs->count();
        }, Lang::get('content::validation.unique_slug')
        );

        Validator::extend(
            'slug_chars', function ( $attribute, $value, $parameters, $validator )
        {
            return $value ? preg_match('#^[a-z0-9/-]+$#', $value) : true;
        }, Lang::get('content::validation.slug_chars')
        );
    }

    /**
     * Each content instance has a one-to-one link to a Content instance
     */
    public function content()
    {
        return $this->morphOne(Content::class, 'model');
    }

    /**
     * Content instances are one to one and loaded once per content instance
     *
     * @var \Reflexions\Content\Models\Content
     */
    var $cached_content;

    /**
     * Mutator to ensure presence of content attribute
     * @return Content
     */
    public function getContentAttribute( $value )
    {
        if( empty($value) )
        {
            $value = $this->cached_content;
        }
        if( empty($value) && isset($this->relations['content']) )
        {
            $value = $this->relations['content'];
            $this->cached_content = $value;
        }
        if( empty($value) )
        {
            $value = $this->content()->first();
            if( $value )
            {
                $this->cached_content = $value;
            }
        }
        if( empty($value) )
        {
            // no content exists for this model yet. Create a blank one.
            $value = Content::fromModel($this);
            $this->cached_content = $value;
        }

        return $value;
    }

    // factored for readability rather than reusability
    use PublishableTrait, SluggableTrait, TaggableTrait, UploadTrait;

    /**
     * Always find with Content
     */
    public static function find( $id )
    {
        return static::with('content')->where('id', $id)->first();
    }

    /**
     * Return self joined with Content.
     * Checks for existing join to ensure only
     * one join to content is added.
     *
     * @return Builder
     */
    public static function scopeWithContent( Builder $query )
    {
        $add_content_join = true;
        $qb = $query->getQuery();
        if( $qb->joins )
        {
            foreach( $qb->joins as $join )
            {
                if( $join->table == "content" )
                {
                    $add_content_join = false;
                    break;
                }
            }
        }

        if( $add_content_join )
        {
            $query = $query->join(
                'content', function ( JoinClause $join )
                {
                    $instance = new static;
                    $table = $instance->getTable();
                    $type = $instance->getMorphClass();
                    $join->on('content.model_id', '=', "$table.id");
                    $join->where('content.model_type', '=', $type);
                }
            );
        }

        return $query;
    }

    // ------------------------------------------------------------------------
    // ### UNTESTED CODE BELOW
    // ------------------------------------------------------------------------

    /**
     * Check if the current object has the given trait.
     * @deprecated Deprecated until this code gets tested
     * @return boolean
     */
    public static function hasTrait( $trait = "" )
    {
        trigger_error("using untested hasTrait method", E_USER_NOTICE);

        return in_array($trait, class_uses(get_called_class()));
    }
}
