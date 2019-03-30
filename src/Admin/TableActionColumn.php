<?php
namespace Reflexions\Content\Admin;

/**
 * Struct for Table Column settings
 */
class TableActionColumn
{
    const ACTIONS_FIELDNAME = "__actions__";
    /**
     * @param string Field name
     * @param string Column label for field
     */
    public function __construct($label)
    {
        $this->label = $label;
        $this->field = self::ACTIONS_FIELDNAME;
        $this->options = [
            "searchable" => false,
            "orderable" => false
        ];
    }
}