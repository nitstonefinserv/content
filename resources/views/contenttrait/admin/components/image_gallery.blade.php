@extends('content::admin.components.field')
@section('widget')
    <div id="{{$attribute}}-id"
        class="ImageGalleryComponent"
        data-label="{{ $label }}"
        data-attribute="{{ $attribute }}"
        data-upload_url="{{ route('content.api-content-file-upload', $content->id ) }}"
        data-upload_max="{{$upload_max}}"
        data-upload_max_bytes="{{ $upload_max_bytes }}"
        data-upload_relative_path_prefix="{{ $relative_path_prefix }}"
        data-upload_url_prefix="{{$upload_url_prefix}}"
        data-api_content_file_delete="{{ route('content.api-content-file-delete', $content->id ) }}"
        data-api_content_file_attribute_update="{{ route('content.api-content-file-attribute-update', $content->id ) }}">
        
        @if (!empty($images))
            @foreach ($images as $image)
            <div class="Image"
                data-id="{{$image->id}}"
                data-url="{{ $image->url }}"
                data-name="{{ $image->name }}"
                data-description="{{ $image->description }}"
                data-link="{{ $image->link }}">
            </div>
            @endforeach
        @endif
    </div>
@overwrite
