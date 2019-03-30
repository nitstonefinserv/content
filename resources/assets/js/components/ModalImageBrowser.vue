<template>
    <div class="content-modal-image-browser">
        <vue-strap-modal :show.sync="browsing" effect="fade" width="400">
            <div slot="modal-header" class="modal-header">
                <button type="button" class="close" v-on:click='closeSelectImageBrowser()' aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Select {{label}}</h4>
            </div>
            <div slot="modal-body" class="modal-body">
                <div class="row" v-for="row in rows">
                    <div class="col-sm-4 image" v-for="image in row">
                        <button type="button" class="close" v-on:click='deleteImage(image)' aria-label="Delete"><span>&times;</span></button>
                        <h5>{{image.name}}</h5>
                        <div class="thumbnail" v-on:click="selectImage(image)">
                            <img v-bind:src="image.url">
                            <caption>
                                <a class="btn btn-default btn-block" role="button">Select</a>
                            </caption>
                        </div>
                    </div>
                </div>
                <div v-if="error" class="alert alert-danger">{{error}}</div>
                <small>Max upload size: {{ upload_max }}</small><br>
                <small v-if="image_help_text !== ''"> {{ image_help_text }}</small>
            </div>
            <div slot="modal-footer" class="modal-footer">
                <button type="button" class="btn btn-default" v-on:click='closeSelectImageBrowser()'>Cancel</button>
                
                <content-upload-button
                    v-on:fail="uploadError"
                    v-on:done="addFile"
                    :atribute="attribute"
                    :upload_url="upload_url"
                    :upload_max="upload_max"
                    :upload_max_bytes="upload_max_bytes"
                    :upload_relative_path_prefix="upload_relative_path_prefix"
                    :upload_url_prefix="upload_url_prefix"
                    ></content-upload-button>
            </div>
        </vue-strap-modal>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                images: [],
                open: false,
                browsing: false,
                error: false,
            };
        },

        props: [
            'label',
            'attribute',
            'image_help_text',
            'upload_url',
            'upload_max',
            'upload_max_bytes',
            'upload_relative_path_prefix',
            'upload_url_prefix',
            'api_content_images_lookup',
            'api_content_file_delete'
        ],

        init: function() {
            this.$on('open-modal-image-browser', function () {
                  this.openSelectImageBrowser();
            });
        },
        computed: {
            rows: function() {
                var rows = [];
                var current_row = [];
                for (var i=0; i< this.images.length;i++) {
                    current_row.push(this.images[i]);
                    if (current_row.length == 3) {
                        rows.push(current_row);
                        current_row = [];
                    }
                }
                if (current_row.length > 0) {
                    rows.push(current_row);
                }
                return rows;
            }
        },
        methods: {
            openSelectImageBrowser: function() {
                var self = this;
                jQuery.ajax(this.api_content_images_lookup, {
                    type: 'GET',
                    data: {  }
                }).success(function (data) {
                    self.images = data.images;
                    self.browsing = true;
                });
            },
            closeSelectImageBrowser: function() {
                this.browsing = false;
            },
            selectImage: function(image) {
                this.browsing = false;
                this.$emit('select-image', image);
            },
            deleteImage: function(image) {
                var self = this;
                jQuery.ajax(this.api_content_file_delete, {
                    type: 'POST',
                    data: { file_id: image.id }
                }).success(function (data) {
                    self.images.$remove(image);
                    self.$dispatch('delete-image', image);
                });
            },
            uploadError: function() {
                this.error = "Upload Error";
            },
            addFile: function(file) {
                this.error = false;
                this.images.push(file);
            }
        }
    };
</script>

<style lang="sass">
.content-modal-image-browser {
    .image {
        h5 {
            overflow:hidden;
            line-height: 1.5;
        }
        .close {
            margin-top: 8px;
        }
    }
}
</style>
