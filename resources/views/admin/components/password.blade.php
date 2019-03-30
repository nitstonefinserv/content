@extends('content::admin.components.field')
@section('widget')
        <?php
        $value =  Form::getValueAttribute($attribute);
        if (preg_match('{^\$2(a|b|y)\$}', $value)) {
            $value = $PASSWORD_PLACEHOLDER;
        }
        ?>
        <input id="{{$attribute }}-id" class="form-control" name="{{ $attribute }}" type="password" value="{{ $value }}">
        <input name="{{ $attribute }}-mutator" type="hidden" value="handlePasswordValue">
@overwrite