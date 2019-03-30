<?php
// ### start view composer
use Reflexions\Content\Admin\Form;
use Reflexions\Content\Admin\Form\Widget;
use Reflexions\Content\Models\Status;
use \Form as LaravelForm;

$datetime_options = [];
$datetime_options['disabled'] = false;
$timestamp = strtotime(LaravelForm::getValueAttribute('publish_date'));
if (empty($timestamp)) {
    $timestamp = time();
}
$datetime_options['timestamp'] = $timestamp;
if (Status::DRAFT == LaravelForm::getValueAttribute('publish_status')) {
    $datetime_options['disabled'] = true;
}
// ### end view composer
?>

{!! Widget::getSelectWidget(
    'publish_status',
    'Publish Status',
    Status::toArray(),
    Form::defaults(['sizing' => 'col-sm-2'])
) !!}

{!! Widget::getDateTimeWidget(
    'publish_date',
    'Publish Date',
    Form::defaults($datetime_options)
) !!}


@if ( $use_slug )
    @include('content::contenttrait.admin.components.preview', ['attribute' => '_preview', 'label' => 'Preview URL'])
    @include('content::contenttrait.admin.components.slug', ['attribute' => 'slug', 'label' => 'Slug'])
    @include('content::contenttrait.admin.components.previous_slugs', ['attribute' => '_previous_slugs', 'label' => 'Previous Slugs'])
@endif


@if ($use_lead_image)
    @include('content::contenttrait.admin.components.image', [
        'attribute' => 'lead_image_id',
        'label' => $image_label,
        'image_help_text' => $image_help_text,
        'content' => $content,
        'current_image' => $lead_image,
        'upload_max' => $upload_max,
        'upload_max_bytes' => $upload_max_bytes,
        'upload_relative_path_prefix' => $relative_path_prefix,
        'upload_url_prefix' => $upload_url_prefix,
    ])
@endif

@section('javascript')
    @parent
    <script language="javascript">
    jQuery(function() {
        var previous_slugs = jQuery('.delete-previous-slug');

        previous_slugs.click(function(e) {
            var button = jQuery(this);
            e.preventDefault();

            jQuery.ajax("{{ route('content.api-delete-previous-slug') }}", {
                type: 'POST',
                data: { id: button.data('id') }
            }).success(function (e) {
                button.remove();
            });
        });
    });
    jQuery(function() {
        var publish_status = jQuery('#publish_status-id'),
            publish_date   = jQuery('#publish_date-date'),
            publish_time   = jQuery('#publish_date-time');
        publish_status.change(function (e) {
            if (jQuery(this).val() == '{{ Status::DRAFT }}' ) {
                publish_date.prop('disabled', true);
                publish_time.prop('disabled', true);
            } else {
                publish_date.prop('disabled', false);
                publish_time.prop('disabled', false);
            }
        });
    });
    </script>
@endsection
