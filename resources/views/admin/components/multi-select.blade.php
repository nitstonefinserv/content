@extends('content::admin.components.field')
@section('widget')
    <select name="{{ $attribute }}[]" multiple="multiple" id="{{ $attribute.'-id' }}" class="form-control">
        @foreach ($values as $key => $value)
            @if (in_array($key, $selected))
                <option selected value="{{ $key }}">{{ $value }}</option>
            @else
                <option value="{{ $key }}">{{ $value }}</option>
            @endif
        @endforeach
    </select>
@overwrite

@section('javascript')
    @parent
    <script language="javascript">
        jQuery(function () {
            $("#{{ $attribute.'-id' }}").select2({
                placeholder: "Select..."
            });
        });
    </script>
@endsection