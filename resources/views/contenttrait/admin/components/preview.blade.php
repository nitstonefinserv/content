@if (!empty($view_link))
<div class="form-group {{ isset($errors) ? $errors->first($attribute, 'has-error') : '' }}">
    {{ Form::label($attribute.'-id', $label, array('id' => $attribute.'-label-id', 'class' => 'col-sm-2 control-label')) }}
    <div class="col-sm-10">
        <p class="help-block">
            <a href="{{ $view_link }}" target="_blank">{{ $view_link }} <i class="fa fa-external-link"></i></a>
        </p>
    </div>
</div>
@endif