@extends('content::admin.components.field')
@section('widget')
    {{ Form::text($attribute, null, [
        'id'        => $attribute.'-id',
        'class'     => 'form-control js-tags'
    ]) }}
@overwrite

@section('javascript')
    @parent
    <script language="javascript">
    jQuery(function() {
        var tags = jQuery('#{{ $attribute }}-id');
        tags.select2({
            tags: true,
            placeholder: 'Enter tags',
            ajax: {
                url: "{{ route('content.api-term-lookup', ['group_slug' => $attribute]) }}",
                dataType: 'json',
                separator: '|',
                quietMillis: 100,
                data: function (term, page) {
                    return {
                        term: term, //search term
                    };
                },
                results: function (data, page) {
                    return { results: data.results };
                },
            },
            initSelection: function(element, callback) {
                return callback({!! $json !!});
            }
        }).select2('val', []);
    });
    </script>
@endsection
