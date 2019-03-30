@extends('content::admin.components.field')
@section('widget')
    {{ Form::select($attribute, ['' => 'Select...']+$values, null, [
        'id'        => $attribute.'-id',
        'class'     => 'form-control js-selector'
    ]) }}
@overwrite
