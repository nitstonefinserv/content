@extends('content::admin.layout-popup')

@push('styles')
    <style type="text/css">
        html, body, .panel { width:100%; height:100%; }
        .panel: { position:relative; }
        .panel-footer {
            position:fixed;
            bottom:0;
            left:0;
            width:100%;
        }
        .panel-footer small {
            margin-right:1em;
            padding-top:7px;
        }
    </style>
@endpush

@push('scripts')
    <script>
    jQuery(function() {
        var editor, grid, upload_btn;

        for(var i in window.opener.CKEDITOR.instances) {
            if (i == "{{ Request::get('CKEditor') }}") {
                editor = window.opener.CKEDITOR.instances[i];
            }
        }

        if (!editor) { return; }

        grid = new {{ $type == 'image' ? 'ContentImageGrid' : 'ContentFileGrid' }}({
            'el' : '#content-grid',
            'propsData' : {
                'api_content_images_lookup' : editor.config.reflexions.api_content_images_lookup,
                'api_content_files_lookup' : editor.config.reflexions.api_content_files_lookup,
                'api_content_file_delete' : editor.config.reflexions.api_content_file_delete
            },
            'events' : {
                'selected': function(file) {
                    window.opener.CKEDITOR.tools.callFunction({{ Request::get('CKEditorFuncNum') }}, file.url, '');
                    window.close();
                }
            }
        });
        grid.load();

        upload_btn = new ContentUploadButton({
            'el' : '#upload-btn-id',
            'propsData' : {
                upload_url : editor.config.reflexions.upload_url,
                upload_max : "{{ Reflexions\Content\Models\File::uploadMax() }}",
                upload_max_bytes : {{ Reflexions\Content\Models\File::uploadMaxBytes() }},
                upload_relative_path_prefix : editor.config.reflexions.upload_relative_path_prefix,
                upload_url_prefix : editor.config.reflexions.upload_url_prefix
            }
        });
        upload_btn.$on('fail', function() {
            jQuery('.alert-danger').show();
        });
        upload_btn.$on('done', function(file) {
            jQuery('.alert-danger').hide();
            grid.addFile(file);
        });
    });
    </script>
@endpush

@section('main-content')
    <div class="panel colourable">
        <div class="panel-heading">
            <span class="panel-title">Select File</span>
        </div>
        <div class="panel-body">
            <div id="content-grid"></div>

            <div style="display:none" class="alert alert-danger">Upload Error</div>
        </div>
        <div class="panel-footer">
            <button type="button" class="btn btn-default" onclick='window.close()'>Cancel</button>
            <div id="upload-btn-id" class="pull-right"></div>
            <small class="pull-right">Max upload size: {{ Reflexions\Content\Models\File::uploadMax() }}</small>
        </div>
    </div>
@endsection
