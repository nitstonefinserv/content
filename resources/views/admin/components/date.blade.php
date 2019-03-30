@extends('content::admin.components.field')
@section('widget')
    <?php
        if (isset($options['timestamp'])) {
            $timestamp = $options['timestamp'];
        } else {
            $val = Form::getValueAttribute($attribute);
            if ($val instanceof DateTimeInterface)
            {
                $timestamp = $val->getTimestamp();
            }
            else
            {
                $timestamp = strtotime($val);
            }
        }
    ?>

    <div class="col-sm-2">
        <div class="input-group">
            <?php
            $attributes = [
                'id' => $attribute.'-id',
                'class' => 'form-control js-datepicker',
                'placeholder' => $label .' Date',
                'autocomplete' => 'off',
            ];
            if ($options['disabled']) {
                $attributes['disabled'] = true;
            }
            $format = isset($options[Reflexions\Content\Admin\Form::OPTIONS_DATE_MUTATE_FORMAT])
                ? $options[Reflexions\Content\Admin\Form::OPTIONS_DATE_MUTATE_FORMAT]
                : "m/d/Y";
            $value = $timestamp
                ? date($format, $timestamp)
                : '';
            ?>
            {{ Form::text($attribute, $value, $attributes) }}
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
@overwrite

