@extends('content::admin.components.field')
@section('widget')
    {{ Form::textarea($attribute, null, [
        'id'        => $attribute.'-id',
        'class'     => 'form-control ckeditor-textarea',
        'rows'      => isset($options['rows']) ? $options['rows'] : '5',
        'data-label' => $label,
        'data-attribute' => $attribute,
        'data-upload_url' => route('content.api-content-file-upload', $content->id ),
        'data-upload_max' => $upload_max,
        'data-upload_max_bytes' => $upload_max_bytes,
        'data-upload_relative_path_prefix' => $relative_path_prefix,
        'data-upload_url_prefix' => $upload_url_prefix,
        'data-api_content_images_lookup' => route('content.api-content-images-lookup', $content->id ),
        'data-api_content_files_lookup' => route('content.api-content-files-lookup', $content->id ),
        'data-api_content_file_delete' => route('content.api-content-file-delete', $content->id ),
        'data-api_content_file_attribute_update' => route('content.api-content-file-attribute-update', $content->id ),
        'data-ckeditor_file_browser' => route('content.ckeditor-file-browser', [$content->id, 'file']),
        'data-ckeditor_image_browser' => route('content.ckeditor-file-browser', [$content->id, 'image']),
    ]) }}
@overwrite
