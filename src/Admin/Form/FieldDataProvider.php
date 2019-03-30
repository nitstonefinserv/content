<?php
namespace Reflexions\Content\Admin\Form;

/**
 * Abstracts one or more fields to be aggregated into a form
 */
interface FieldDataProvider
{
    /**
     * Returns the attributes to be edited by this widget
     * @return array
     */
    public function getAttributes();

    /**
     * Returns the validation rules associated with this widget
     * @return array
     */
    public function getRules();

    /**
     * Returns the views associated with this widget.
     * @return array
     */
    public function getViews();

    /** 
     * Returns any attribute mutators associated with this widget.
     * @return array
     */
    public function getMutators();

    /**
     * Returns any post-save handlers associated with this widget.
     * @return array
     */
    public function getSavedHandlers();
}
