@extends(Config::get('content.admin-layout'))

@section('breadcrumb')
    <li class="active"><a href="{{ URL::route('admin-'.$config->name().'-index') }}">{{$config->label()}}</a></li>
@endsection

@section('page-header')
    <i class="fa {{$config->pageIcon()}} page-header-icon"></i>&nbsp;&nbsp;{{$config->label()}}

    <div class="btn-group" role="group" aria-label="Actions" style="margin-left: .5em;">
        @foreach($config->listActions() as $action)
            <a class="btn btn-sm btn-default export" href="{{ $action->link }}">
                <i class="fa {{$action->icon}}"></i> {{$action->label}}
            </a>
        @endforeach
    </div>
@endsection

@section('main-content')
    {{--
    @if ( Request::session()->has('flash_message') )
        <div class="alert alert-{{ session('flash_message.level') }} fade in">
            <strong>{{ session('flash_message.title') }}</strong> {{ session('flash_message.message') }}

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    --}}

    <div class="table" id="admin-{{$config->name()}}-table">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                @foreach($config->tableColumns() as $column)
                    <th style="{{ isset($column->width) ? 'min-width: ' . $column->width . ';' : '' }}">{{$column->label}}</th>
                @endforeach
                </tr>
            </thead>
        </table>
    </div>

@endsection


@section('style')
    <style>
        td:last-of-type { white-space: nowrap; }
    </style>
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


@section('javascript')
    <script type="text/javascript">
    jQuery(function() {
        var render_action_column = function(data, type, row, meta) {
                return jQuery.map(JSON.parse(data), function(button, i) {
                    return '<a href="'+button.link+'" class="btn btn-sm ' + (button.type == 'delete' ? 'btn-danger delete' : '') + '"><i class="'+button.icon+'"></i> '+button.label+'</a>';
                }).join(' ');
            },
            render_status_column = function(data, type, row, meta) {
                var type = "warning",
                    text = "Draft";

                if ( data == 'archived' ) {
                    type = "default";
                    text = "Archived";
                } else if ( data == 'scheduled' ) {
                    type = "info";
                    text = "Scheduled";
                } else if ( data == 'published' ) {
                    type = "success";
                    text = "Published";
                }

                return '<label class="label label-' + type + '">' + text + '</label>';
            },
            server_columns = {!!
                json_encode(array_map(function($column) {
                    $info = ['name' => $column->field];
                    $info = array_merge($info, $column->options);
                    return $info;
                }, $config->tableColumns()))
            !!},
            columns = jQuery.map(server_columns, function(column, i) {
                if (column.name == '__actions__') {
                    column.render = render_action_column;
                } else if (column.name == 'publish_status') {
                    column.render = render_status_column;
                }
                return column;
            }),
            table = jQuery('#admin-{!! $config->name() !!}-table table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{URL::route('admin-'.$config->name().'-datatables')}}?status={{Request::get('status')}}",
                "iDisplayLength": {!! $config->pagesize() !!},
                "autoWidth": false,
                "order": {!! json_encode($config->order() ) !!},
                "columns": columns,
            });

        table.on( 'draw.dt', function () {
            jQuery('a.export')[0].search = jQuery.param(table.ajax.params());
        } );
        jQuery('#admin-{!! $config->name() !!}-table .dataTables_filter input').attr('placeholder', 'Search...');


        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var delete_btn = jQuery(this);
            bootbox.confirm({
                message: "Are you sure?",
                callback: function(result) {
                    if (result) {
                        $.ajax({
                            url: delete_btn.attr('href'),
                            method: 'DELETE',
                            success: function (success) {
                                if (success == true) {
                                    delete_btn.parents('tr').remove().end();
                                    sweetAlert('Success', 'Successfully deleted', 'success')
                                } else {
                                    sweetAlert('Error', success, 'error');
                                }
                            }
                        });
                    }
                },
                className: "bootbox-sm"
            });
        });

        function sweetAlert (title, message, type) {
            swal({
                title: title,
                html: message,
                type: type,
                timer: 2000,
                allowEscapeKey: true,
                allowOutsideClick: true,
                showConfirmButton: false
            });
        }
    });
    </script>
@endsection
