<?php namespace Reflexions\Content\Models;

class Slug extends \Eloquent
{

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    /**
     * Validate the given slug
     * @param  string $slug
     * @return boolean
     */
    public static function validate( $slug = '' )
    {
        if( !preg_match('#^[a-z0-9/-]*$#', $slug) )
        {
            print "regex\n";
            return false;
        }

        if( !strlen($slug) || strlen($slug) > 255 )
        {
            print "strlen\n";
            return false;
        }

        return true;
    }

    public static function existing_slugs( $model_type, $model_id = null )
    {
        return Slug::whereHas(
            'content', function ( $query ) use ( $model_type, $model_id )
        {
            $query->where('content.model_type', Content::morph($model_type));
            if( $model_id )
            {
                $query->where('content.model_id', '!=', $model_id);
            }
        }
        );
    }


    /**
     * Normalize slug by splitting on slashes, then calling str_slug
     */
    public static function normalize( $slug )
    {
        return implode(
            '/',
            array_map(
                'str_slug',
                explode('/', $slug)
            )
        );
    }
}
