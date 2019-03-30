@extends('content::admin.components.field')
@section('widget')
    <div id="{{$attribute}}-id"
        class="FileListComponent"
        data-label="{{ $label }}"
        data-attribute="{{ $attribute }}"
        data-upload_url="{{ route('content.api-content-file-upload', $content->id ) }}"
        data-upload_max="{{$upload_max}}"
        data-upload_max_bytes="{{ $upload_max_bytes }}"
        data-upload_relative_path_prefix="{{ $relative_path_prefix }}"
        data-upload_url_prefix="{{$upload_url_prefix}}"
        data-api_content_file_delete="{{ route('content.api-content-file-delete', $content->id ) }}"
        data-api_content_file_attribute_update="{{ route('content.api-content-file-attribute-update', $content->id ) }}">
        
        @if (!empty($files))
            @foreach ($files as $file)
            <div class="File"
                data-id="{{$file->id}}"
                data-url="{{ $file->url }}"
                data-name="{{ $file->name }}"
                data-description="{{ $file->description }}">
            </div>
            @endforeach
        @endif
    </div>
@overwrite
