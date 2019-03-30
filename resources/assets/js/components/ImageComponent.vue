<template>
    <div class="image-field">
        <template v-if="current_image">
            <p>
                <h5>{{current_image.name}}</h5>
                <img class="current_image" v-bind:src="current_image.url">
                <input type="hidden" name="{{attribute}}" v-bind:value="current_image.id">
            </p>
        </template>
        <div class="input-group">
            <a class="btn btn-default select-image" v-on:click="openSelectImageBrowser()">
                Select Image <i class="fa fa-picture-o"></i>
            </a>
            <a class="btn btn-link" v-if="current_image" v-on:click='clearImage()'>Clear {{label}}</a>
        </div>

        <content-modal-image-browser
            v-on:select-image="selectImage"
            v-on:delete-image="deleteImage"
            :label="label"
            :attribute="attribute"

            :image_help_text="image_help_text"
            :upload_url="upload_url"
            :upload_max="upload_max"
            :upload_max_bytes="upload_max_bytes"
            :upload_relative_path_prefix="upload_relative_path_prefix"
            :upload_url_prefix="upload_url_prefix"
            :api_content_images_lookup="api_content_images_lookup"
            :api_content_file_delete="api_content_file_delete"
            ></content-modal-image-browser>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                attribute: null,
                label: null,
                current_image: null,

                image_help_text: null,
                upload_url: false,
                upload_max: false,
                upload_max_bytes: false,
                upload_relative_path_prefix: false,
                upload_url_prefix: false,
                api_content_images_lookup: false,
                api_content_file_delete: false,
            };
        },
        methods: {
            openSelectImageBrowser: function() {
                this.$broadcast('open-modal-image-browser');
            },
            selectImage: function(image) {
                this.current_image = image;
            },
            deleteImage: function(image) {
                if (this.current_image && this.current_image.id == image.id) {
                    this.current_image = null;
                }
            },
            clearImage: function() {
                this.current_image = null;
            }
        }
    };
</script>

<style lang="sass">
.image-field {
    .current_image {
        max-height:100px;
        mad-width:100px;
    }
    .btn-link, .btn-link:hover {
        background:none !important;
        border:none;
    }
    .controls {
        clear:both;
    }
}
</style>
