@extends('content::admin.components.field')
@section('widget')
    {{ Form::text(
        $attribute,
        null,
        array_merge(
            [
                'id' => $attribute.'-id',
                'class' => 'form-control'
            ],
            $options
        )
    ) }}
@overwrite
