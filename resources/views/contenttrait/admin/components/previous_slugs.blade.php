<?php
$previous_slugs = null;
if ($content->id) {
    $previous_slugs = $content->slugs();
    if ($content->slug) {
        $previous_slugs = $previous_slugs->where('slug', '!=', $content->slug);
    }
}
?>
@if ($previous_slugs && $previous_slugs->count())
    <div class="form-group {{ isset($errors) ? $errors->first($attribute, 'has-error') : '' }}">
        <div class="col-sm-9 col-sm-offset-2">
            <p class="help-block"><strong>Previous Slugs</strong></p>

            <p class="help-block">
                @foreach($previous_slugs->get() as $slug)
                    <a href="#"
                        class="btn btn-outline btn-xs btn-labeled delete-previous-slug"
                        data-id="{{$slug->id}}">/{{$slug->slug}} <i class="fa fa-times btn-label"></i>
                    </a>
                @endforeach
            </p>
        </div>
    </div>
@endif
