<?php namespace Reflexions\Content\Traits;

use Illuminate\Database\Eloquent\Builder;
use Reflexions\Content\Models\Content;
use Reflexions\Content\Models\Slug;
use Reflexions\Content\Models\Status;

/**
 * Factored for readability rather than reusability
 * @see \Reflexions\Content\ContentTrait
 *
 * @property string $slug
 */
trait SluggableTrait
{

    public static function bootSluggableTrait()
    {
        static::saving(
            function ( $model )
            {
                $content = $model->content;
                if( $content->slug )
                {
                    $query = Slug::existing_slugs($content->model_type, $content->model_id)
                        ->where('slug', $content->slug);
                    $existing = $query->get();
                    if( $existing->count() )
                    {
                        throw new \Exception('Slug already exists on different content item.');
                    }
                }
            }
        );

        static::saved(
            function ( $model )
            {
                /** @var ContentTrait $content */
                $content = $model->content;
                if( $content->slug )
                {
                    $slug = $content->slugs()
                        ->where('slugs.slug', $content->slug)->first();
                    if( empty($slug) )
                    {
                        $slug = new Slug();
                    }
                    $slug->slug = $content->slug;
                    $slug->content_id = $content->id;
                    $slug->save();
                }
            }
        );
    }

    /**
     * Get current slug
     *
     * @return string
     */
    public function getSlugAttribute( $value )
    {
        return $this->content->slug;
    }

    /**
     * Set current slug
     *
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function setSlugAttribute( $value )
    {
        if( empty($value) )
        {
            if( !empty($this->title) )
            {
                return $this->setSlugAttribute(Slug::normalize($this->title));
            }
            elseif( !empty($this->name) )
            {
                return $this->setSlugAttribute(Slug::normalize($this->name));
            }
            else
            {
                // set content's slug null and return null
                return $this->content->slug = null;
            }
        }
        if( strlen($value) > 255 )
        {
            throw new \Exception('Invalid slug length');
        }

        return $this->content->slug = static::findUniqueSlug(
            Slug::normalize($value),
            $this->content->model_type,
            $this->content->model_id
        );
    }

    /**
     * Get all valid slugs for the provided instance
     * @return array[string]
     */
    public function getAllSlugs()
    {
        return array_map(
            function ( $slug )
            {
                return $slug['slug'];
            },
            Slug::where('content_id', $this->content->id)
                ->orderBy('updated_at', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->toArray()
        );
    }

    /**
     * Remove slug association with model.
     *
     * @param string $slug the slug to be removed
     * @throws \Exception
     */
    public function removeSlug( $slug )
    {
        if( empty($this->content->id) )
        {
            throw new \Exception('Invalid operation: instance not saved yet.');
        }
        $doomed = Slug::where('content_id', $this->content->id)
            ->where('slug', $slug)->get();
        foreach( $doomed as $slug )
        {
            $slug->delete();
        }
        $slugs = $this->getAllSlugs();
        $this->slug = empty($slugs)
            ? null
            : array_pop($slugs);
        $this->save();
    }


    /**
     * Returns a query to find object instance by slug
     *
     * @return Builder
     */
    public static function queryBySlug( $slug )
    {
        if( !mb_check_encoding($slug, 'UTF-8') )
        {
            // instead of sending the invalid data to the database for it to
            // error, we'll attempt to reinterpret it as Windows-1252

            $slug = mb_convert_encoding($slug, 'UTF-8', 'Windows-1252');
        }

        $query = static::withContent();

        if( !\Auth::check() )
        {
            $query = $query->published();
        }

        $query = $query->whereIn(
            'content.content_id',
            function ( $query ) use ( $slug )
            {
                $query
                    ->select('slugs.content_id')
                    ->from('slugs')
                    ->where('slugs.slug', '=', $slug);
            }
        )
            ->orderBy('content.updated_at', 'DESC');

        return $query;
    }


    /**
     * Find object instance by slug
     *
     * @return static
     */
    public static function findBySlug( $slug )
    {
        $query = static::queryBySlug($slug);
        //$sql = $query->toSql();
        return $query->first();
    }

    /**
     * create unique slug based on the provided string
     */
    public static function findUniqueSlug( $slug, $model_type, $model_id = null )
    {
        $slug = Slug::normalize($slug);
        $existing = Slug::existing_slugs($model_type, $model_id)
            ->where('slug', '=', $slug)
            ->first();

        if( !$existing )
        {
            return $slug;
        }

        $index = 2;
        $sanity_limit = 1000;
        while( $index < $sanity_limit )
        {
            $new_slug = $slug . '-' . $index;

            $existing = Slug::existing_slugs($model_type, $model_id)
                ->where('slug', '=', $new_slug)
                ->first();

            if( $existing )
            {
                $index++;
            }
            else
            {
                return $new_slug;
            }
        }
        throw new \Exception("Unique slug generator limit reached");
    }
}
