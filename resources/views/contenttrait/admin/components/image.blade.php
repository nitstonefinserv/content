@extends('content::admin.components.field')
@section('widget')
    <div id="{{$attribute}}-id"
        class="ImageComponent"
        data-label="{{ $label }}"
        data-image_help_text="{{ $image_help_text }}"
        data-attribute="{{ $attribute }}"
        data-upload_url="{{ route('content.api-content-file-upload', $content->id ) }}"
        data-upload_max="{{$upload_max}}"
        data-upload_max_bytes="{{ $upload_max_bytes }}"
        data-upload_relative_path_prefix="{{ $relative_path_prefix }}"
        data-upload_url_prefix="{{$upload_url_prefix}}"
        data-api_content_images_lookup="{{ route('content.api-content-images-lookup', $content->id ) }}"
        data-api_content_file_delete="{{ route('content.api-content-file-delete', $content->id ) }}"
        data-api_content_file_attribute_update="{{ route('content.api-content-file-attribute-update', $content->id ) }}">
        
        @if ($current_image)
        <div class="CurrentImage"
            data-id="{{$current_image->id}}"
            data-url="{{ $current_image->url }}"
            data-name="{{ $current_image->name }}">
        </div>
        @endif
    </div>
@overwrite
