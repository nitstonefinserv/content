<div class="form-group {{ isset($errors) ? $errors->first($attribute, 'has-error') : '' }}">
    @if (isset($label))
        {{ Form::label($attribute.'-id', $label, array('id' => $attribute.'-label-id', 'class' => 'col-sm-2 control-label')) }}
    @endif
    <div class="{{ isset($options['sizing']) ? $options['sizing'] : 'col-sm-10' }}">
        
        {!! isset($options['pre']) || isset($options['post']) ? '<div class="input-group">' : '' !!}
            @if (isset($options['pre']))
                <span class="input-group-addon">{{ $options['pre'] }}</span>
            @endif
            
            @yield('widget')
            
            @if (isset($options['post']))
                <span class="input-group-addon">{{ $options['post'] }}</span>
            @endif
        {!! isset($options['pre']) || isset($options['post']) ? '</div>' : '' !!}

        @if (isset($options['help']))
            <p class="help-block">{{ $options['help'] }}</p>
        @endif

        {!! isset($errors) ? $errors->first($attribute, '<div class="help-block">:message</div>') : '' !!}
    </div>
</div>