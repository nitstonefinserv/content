<?php
namespace Reflexions\Content\Admin;

/**
 * Struct for Table Status Column settings
 */
class TableStatusColumn
{
    const STATUS_FIELDNAME = "publish_status";
    /**
     * @param string Field name
     * @param string Column label for field
     */
    public function __construct($label)
    {
        $this->label = $label;
        $this->field = self::STATUS_FIELDNAME;
        $this->options = [
            "searchable" => false,
        ];
    }
}