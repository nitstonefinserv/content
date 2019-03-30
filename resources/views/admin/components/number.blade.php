@extends('content::admin.components.field')
@section('widget')
    {{ Form::number($attribute, null, [
        'id'        => $attribute.'-id',
        'class'     => 'form-control',
        'step'   => isset($options['attributes']['step']) ? $options['attributes']['step'] : null,
    ]) }}
@overwrite