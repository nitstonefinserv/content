<?php
use Reflexions\Content\Admin\Form\Widget;
use Reflexions\Content\Admin\Form;

$label = $group == 'default' ? 'Tags' : $group;

$json = json_encode(array_values(array_map(
    function ($t) {
        return [
            'id' => str_slug($t),
            'text' => $t
        ];
    },
    $model->getTagNames($group)
)));
?>

@include('content::contenttrait.admin.components.tags', [
    'label' => $label,
    'attribute' => $group_slug,
    'json' => $json
])