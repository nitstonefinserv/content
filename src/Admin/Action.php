<?php
namespace Reflexions\Content\Admin;

/**
 * Struct for Action settings
 */
class Action
{
    /**
     * @param string Field name
     * @param string Column label for field
     */
    public function __construct($label, $icon, $link, $type=null)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->link = $link;
        $this->type = $type;
    }
}