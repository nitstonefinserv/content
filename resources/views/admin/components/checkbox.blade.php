@extends('content::admin.components.field')
@section('widget')
    <div class="js-switcher">
        {!! Form::checkbox($attribute, true, Form::getValueAttribute($attribute)) !!}
    </div>
@overwrite


@push('scripts')
    <script>
        $('[name="{{ $attribute }}"]').switcher({
            theme: 'square',
            on_state_content: '{{ $options["on_content"] or 'Yes' }}',
            off_state_content: '{{ $options["off_content"] or 'No' }}'
        });
    </script>
@endpush