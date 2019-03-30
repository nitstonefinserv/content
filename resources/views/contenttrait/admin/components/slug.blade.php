@extends('content::admin.components.field')
@section('widget')
    <div class="input-group">
        <span class="input-group-addon">/</span>
        {{ Form::text(
            $attribute,
            null,
            [
                'id'        => $attribute.'-id',
                'class'     => 'form-control'
            ]
        ) }}
    </div>
@overwrite
