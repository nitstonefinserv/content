<?php
namespace Reflexions\Content\Admin\Form;

/**
 * Struct to hold field data
 */
class Field implements FieldDataProvider
{
    public function __construct($attribute, $rule, \Illuminate\Contracts\View\View $view, callable $mutator=null, callable $saved=null)
    {
        $this->attribute = $attribute;
        $this->rule = $rule;
        $this->view = $view;
        $this->mutator = $mutator;
        $this->saved = $saved;
    }

    public function getAttributes()
    {
        return [$this->attribute];
    }

    public function getRules()
    {
        return [$this->attribute => $this->rule];
    }

    public function getViews()
    {
        return [$this->view];
    }

    public function getMutators()
    {
        return [$this->attribute => $this->mutator];
    }
    public function getSavedHandlers()
    {
        return [$this->attribute => $this->saved];
    }
}
