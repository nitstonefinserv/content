@extends(Config::get('content.admin-layout'))

@section('breadcrumb')
    <li><a href="{{ URL::route('admin-'.$config->name().'-index') }}">{{$config->label()}}</a></li>
    @if ( str_contains(Route::currentRouteName(), 'edit') )
        <li class="active"><a href="{{ URL::route('admin-'.$config->name().'-edit', $model->id) }}">Edit</a></li>
    @else
        <li class="active"><a href="{{ URL::route('admin-'.$config->name().'-create') }}">Create</a></li>
    @endif
@endsection

@section('page-header')
    <i class="fa {{$config->pageIcon()}} page-header-icon"></i>&nbsp;&nbsp;{{ str_contains(Route::currentRouteName(), 'edit') ? 'Edit' : 'Create' }} {{str_singular($config->label())}}
@endsection

@section('main-content')
    {{ Form::model($model, [
        'id'    => 'admin-'.$config->name().'-edit-form',
        'name'  => 'admin-'.$config->name().'-edit-form',
        'class' => 'form-horizontal',
        'route' => array($model->id ? 'admin-'.$config->name().'-update' : 'admin-'.$config->name().'-store', $model->id),
        'method' => ($model->id) ? 'patch' : 'post',
        'data-id' => ($model->id) ? $model->id : 0
    ]) }}
    <div class="panel">
        <div class="panel-body">
            @include('content::admin.components.save-cancel', ['name' => $config->name()])
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            @foreach($form->getFieldViews() as $view)
                {!! $view !!}
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            @include('content::admin.components.save-cancel', ['name' => $config->name()])
        </div>
    </div>
    {{ Form::close() }}

@endsection


@section('flash-message')
    
    {{-- Outputs code for SweetAlert if session has a flash message --}}
    @if ( session()->has('flash_message') )
        <script type="text/javascript">
            swal({
                title: "{{ session('flash_message.title') }}",
                html: "{{ session('flash_message.message') }}",
                type: "{{ session('flash_message.level') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if ( session()->has('flash_message_overlay') )
        <script type="text/javascript">
            swal({
                title: "{{ session('flash_message_overlay.title') }}",
                html: "{!! is_array(session('flash_message_overlay.message')) ? join('<br>', session('flash_message_overlay.message')) : session('flash_message_overlay.message') !!}",
                type: "{{ session('flash_message_overlay.level') }}",
                confirmButtonText: "Ok",
                allowEscapeKey: true,
                allowOutsideClick: true
            });
        </script>
    @endif

@stop

@push('styles')
    <style>
        .dropdown-menu {
            position: absolute !important;
        }
    </style>
@endpush

@section('javascript')
    <script type="text/javascript">
        jQuery(function() {
            jQuery(".js-datepicker").datepicker();
            jQuery(".js-timepicker").timepicker({defaultTime: false});
            jQuery(".hidden.datetime").each(function() {
                var id = jQuery(this).attr('id'),
                    date = "#"+id+"-date",
                    time = "#"+id+"-time";
                jQuery(date + ', ' + time).change(function() {
                    jQuery("#"+id).val(jQuery(date).val()+' '+jQuery(time).val());
                });
            });
            jQuery(".js-checkbox").switcher({
                on_state_content: "Yes",
                off_state_content: "No"
            });
            jQuery(".js-selector").select2({
                minimumResultsForSearch: 10
            });
        });
    </script>
@endsection
