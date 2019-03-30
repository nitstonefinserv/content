@extends(Config::get('content.admin-layout'))

@section('breadcrumb')
    <li><a href="{{ URL::route("admin-{$config_name}-index") }}">Role</a></li>
    <li class="active"><a href="{{ URL::route("admin-{$config_name}-edit-all") }}">Edit All</a></li>
@endsection

@section('page-header')
    <i class="fa fa-user-plus page-header-icon"></i>&nbsp;&nbsp;Edit Roles
@endsection

@section('main-content')
    {{ Form::open([
        'id'    => "admin-{$config_name}-edit-all-form",
        'name'  => "admin-{$config_name}-edit-all-form",
        'class' => 'form-horizontal',
        'route' => "admin-{$config_name}-update-all",
        'method' => 'patch'
    ]) }}
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 col-sm-2">
                    <a href="{{ URL::route("admin-{$config_name}-index") }}" class="btn btn-danger">Cancel, Go back</a>
                </div>

                <div class="col-xs-6 col-sm-10">
                    <div class="pull-right">
                        <button class="btn btn-success" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="table-light">
                <div class="table-header">
                    <div class="table-caption">
                        Roles | Permissions
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        @foreach($roles as $role)
                            <th>{{ $role->name }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $slug => $permission)
                        <tr>
                            <td>{{ $permission }}</td>
                            @foreach($roles as $role)
                                <td>
                                    {!!
                                        Form::checkbox(
                                            'permissions[' . $role->name . '][]',
                                            $slug,
                                            in_array($slug, json_decode($role->permissions, true) ?: [])
                                        )
                                    !!}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 col-sm-2">
                    <a href="{{ URL::route("admin-{$config_name}-index") }}" class="btn btn-danger">Cancel, Go back</a>
                </div>

                <div class="col-xs-6 col-sm-10">
                    <div class="pull-right">
                        <button class="btn btn-success" type="submit">Save</button>
                    </div>
                </div>
            </div>
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
