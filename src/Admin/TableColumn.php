<?php
namespace Reflexions\Content\Admin;

/**
 * Struct for Table Column settings
 */
class TableColumn
{
    /**
     * @param string Field name
     * @param string Column label for field
     */
    public function __construct($field, $label, $options=[], $width='')
    {
        $this->field = $field;
        $this->label = $label;
        $this->options = $options;
        $this->width = $width;
    }
}