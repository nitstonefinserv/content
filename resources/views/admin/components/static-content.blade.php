@extends('content::admin.components.field')
@section('widget')
    {{ HTML::tag('p', Form::getValueAttribute($attribute), [
        'class'     => 'form-control-static',
    ]) }}
    {{ Form::hidden($attribute, Form::getValueAttribute($attribute), [
        'id' => $attribute.'-id'
    ]) }}
@overwrite