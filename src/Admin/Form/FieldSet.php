<?php
namespace Reflexions\Content\Admin\Form;

/**
 * Struct to hold fieldset data
 */
class FieldSet implements FieldDataProvider
{
    public function __construct($attributes, $rules, $views, $mutators=[], $saved=[])
    {
        $this->attributes = $attributes;
        $this->rules = $rules;
        $this->views = $views;
        $this->mutators = $mutators;
        $this->saved = $saved;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function getMutators()
    {
        return $this->mutators;
    }

    public function getSavedHandlers()
    {
        return $this->saved;
    }
}