<?php namespace Reflexions\Content\Traits;

use Illuminate\Database\Eloquent\Builder;
use Reflexions\Content\Models\Status;
use Carbon\Carbon;
use Reflexions\Content\Admin\Form\FieldSet;

use View;
use Content;

/**
 * Factored for readability rather than reusability
 * @see \Reflexions\Content\ContentTrait
 */
trait PublishableTrait {
    
    /**
     * Mutators to support $instance->publish_status
     */
    public function getPublishStatusAttribute()
    {
        $value = $this->content->publish_status;
        $publish_date = date('Y-m-d H:i:s', strtotime($this->content->publish_date));

        if ($value === Status::PUBLISHED && $publish_date > Carbon::now()) {
            return Status::SCHEDULED;
        }
        return $value;
    }
    
    public function setPublishStatusAttribute($value)
    {
        return $this->content->publish_status = $value;
    }

    /**
     * Mutators to support $instance->publish_status
     */
    public function getPublishDateAttribute()
    {
        return $this->content->publish_date;
    }
    
    public function setPublishDateAttribute($value)
    {
        return $this->content->publish_date = $value;
    }

    /**
     * Mutator to support $instance->publish_status_label
     */
    public function getPublishStatusLabelAttribute()
    {
        return Status::label($this->publish_status);
    }

    /**
     * Return true if status is new
     * @return boolean
     */
    public function isStub()
    {
        return $this->publish_status == Status::STUB;
    }

    /**
     * Return true if status is draft
     * @return boolean
     */
    public function isDraft()
    {
        return $this->publish_status == Status::DRAFT;
    }

    /**
     * Return true if status is scheduled
     * @return boolean
     */
    public function isScheduled()
    {
        return $this->publish_status == Status::SCHEDULED;
    }

    /**
     * Return true if status is published
     * @return boolean
     */
    public function isPublished()
    {
        return $this->publish_status == Status::PUBLISHED;
    }

    /**
     * Return true if status is archived
     * @return boolean
     */
    public function isArchived()
    {
        return $this->publish_status == Status::ARCHIVED;
    }

    /**
     * Query Builder helper
     * @return \illuminate\Database\Eloquent\Builder
     */
    public static function scopePublished(Builder $query)
    {
        return $query
            ->withContent()
            ->where('content.publish_status', '=', Status::PUBLISHED)
            ->where('content.publish_date', '<=', Carbon::now());
    }

    /**
     * Provides publish status and slug fields for use in Reflexions\Content\Admin
     */
    public function getPublishableAdminFields($options=[])
    {
        $view_link = null;
        if (isset($options['view_route'])) {
            $view_link = $this->slug
                ? route($options['view_route'], ['slug' => $this->slug])
                : null;
        }
        $content = $this->content;

        $use_lead_image = !isset($options['use_lead_image']) || $options['use_lead_image'];

        if ($use_lead_image) {
            $relative_path_prefix = $options['relative_path_prefix'];
            $upload_url_prefix = $options['upload_url_prefix'];
        }

        $upload_max = \Reflexions\Content\Models\File::uploadMax();
        $upload_max_bytes = \Reflexions\Content\Models\File::uploadMaxBytes();
        $lead_image = $content->leadImage()->first();
        $image_label = isset($options['image_label']) ? $options['image_label'] : 'Lead Image';
        $image_help_text = isset($options['image_help_text']) ? $options['image_help_text'] : '';

        $fields = collect([
            'publish_status',
            'publish_date',
            'lead_image_id',
        ]);
        $rules = collect([
            'publish_status' => 'in:' . implode(',', array_keys(Status::toArray())),
            'publish_date' => 'date',
        ]);

        // Allows slug to not be included if not needed, true/false
        $use_slug = !isset($options['use_slug']) || $options['use_slug'];
        if ( $use_slug ) {
            $fields->prepend('slug');
            $rules->prepend(
                "slug_chars|unique_slug:{$this->model_type},{$this->model_id}",
                'slug'
            );
        }

        return new FieldSet(
            $fields->toArray(),
            $rules->toArray(),
            [View::make(
                Content::package().'::contenttrait.admin.publishable',
                compact(
                    'view_link',
                    'content',
                    'image_label',
                    'image_help_text',
                    'relative_path_prefix',
                    'upload_url_prefix',
                    'upload_max',
                    'upload_max_bytes',
                    'lead_image',
                    'use_slug',
                    'use_lead_image'
                )
            )],
            [
                'publish_status' => function ($value) {
                    if (empty($value)) {
                        $value = Status::DRAFT;
                    }
                    return $value;
                }
            ]
        );
    }

    // ------------------------------------------------------------------------
    // ### UNTESTED CODE BELOW
    // ------------------------------------------------------------------------

    /**
     * Get human readable status 
     * @return string
     */
    public function statusStr()
    {
        return Status::get($this->publish_status);
    }

    /**
     * Return date since published in plain English.
     * @return string
     */
    public function publishedString() 
    {
        $time = time() - strtotime($this->publish_date);
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'') . ' ago';
        }
    }
}
