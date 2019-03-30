@extends('content::admin.components.field')
@section('widget')
    
    {{ Form::textarea($attribute, null, [
        'id'        => $attribute.'-id',
        'class'     => 'form-control' . (isset($options['resize']) ? ' form-control--no-resize' : ''),
        'rows'      => isset($options['rows']) ? $options['rows'] : '5',
        'maxlength' => isset($options['maxlength']) ? $options['maxlength'] : ''
    ] + $options) }}

    <style>
        .form-control--no-resize {
            resize: none;
        }
    </style>
@overwrite
