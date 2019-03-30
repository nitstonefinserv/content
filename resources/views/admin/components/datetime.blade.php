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
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            <div class="input-group date js-datepicker">
                <?php
                    $attributes = [
                        'id' => $attribute.'-date',
                        'class' => 'form-control',
                        'placeholder' => 'Date',
                        // 'autocomplete' => 'off'
                    ];
                    if (!empty($options['disabled'])) {
                        $attributes['disabled'] = true;
                    }
                    $value = $timestamp
                        ? date("m/d/Y", $timestamp)
                        : '';
                ?>
                {{ Form::text($attribute.'-date', $value, $attributes) }}
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>

             <p class="help-block">Format: mm/dd/yyyy</p>

        </div>

        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            <div class="input-group">
                <?php
                    $attributes = [
                        'id' => $attribute.'-time',
                        'class' => 'form-control js-timepicker',
                        'placeholder' => 'Time',
                        // 'autocomplete' => 'off'
                    ];
                    if (!empty($options['disabled'])) {
                        $attributes['disabled'] = true;
                    }
                    $value = $timestamp
                        ? date("h:i:sA", $timestamp)
                        : '';
                ?>
                {{ Form::text($attribute.'-time',  $value, $attributes) }}
                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
            </div>
        </div>
    </div>
    
    <?php
        // Space is consistent with how admin/edit.blade.php's js calculates the value for .hidden.datetime
        $value = empty($timestamp) ? ' ' : date("m/d/Y h:i:sA", $timestamp);
    ?>
    {{ Form::hidden($attribute, $value, [
        'id' => $attribute,
        'class' => 'datetime hidden'
    ]) }}
@overwrite
