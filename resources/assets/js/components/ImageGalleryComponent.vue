<template>
    <div class="image-gallery">
        <div class="image-list">
            <div class="image thumbnail" v-for="image in images" v-bind:data-id="image.id">
                <img v-bind:src="image.url">
                <button type="button" class="close" v-on:click='deleteImage(image)' aria-label="Delete"><span>&times;</span></button>
                <h5><a class="editable" data-type="text" data-name="name" data-title="Enter Name" v-bind:data-pk="image.id">{{image.name}}</a></h5>
                <p><a class="editable"
                    data-type="textarea"
                    data-name="description"
                    data-title="Enter Description"
                    data-placeholder="Description"
                    v-bind:data-pk="image.id">{{ image.description }}</a></p>
                <p><a class="editable"
                    data-type="text"
                    data-name="link"
                    data-title="Enter url to link to"
                    v-bind:data-pk="image.id">{{ image.link }}</a></p>
                <div style="clear:both"></div>
            </div>
        </div>

        <input type="hidden" v-bind:name="attribute" v-bind:value="getIDString()">

        <div class="controls">
            <div v-if="error" class="alert alert-danger">{{error}}</div>
            <small>Max upload size: {{ upload_max }}</small>

            <content-upload-button
                v-on:fail="uploadError"
                v-on:done="addFile"
                :attribute="attribute"
                :upload_url="upload_url"
                :upload_max="upload_max"
                :upload_max_bytes="upload_max_bytes"
                :upload_relative_path_prefix="upload_relative_path_prefix"
                :upload_url_prefix="upload_url_prefix"
                ></content-upload-button>
        </div>
    </div>
</template>


<style lang="sass">
.image-gallery{
    .image {
        h5 {
            overflow:hidden;
            line-height: 1.5;
            margin-top:0;
            .editable {
                margin-top:0;
            }
        }
        .close {
            margin-top: 8px;
        }
        img {
            max-height: 100px;
            max-width: 100px;
            float: left;
            margin-right: 5px;
        }
        .close {
            margin-top:0;
        }
        width: 100%;
        margin: 5px;
        clear: both;
    }

    .controls {
        clear:both;
    }
}
</style>

<script>
    export default {
        data: function() {
            return {
                images: [],
                label: false,
                attribute: false,
                error: false,
                upload_url: false,
                upload_max: false,
                upload_max_bytes: false,
                upload_relative_path_prefix: false,
                upload_url_prefix: false,
                api_content_images_lookup: false,
                api_content_file_delete: false,
                api_content_file_attribute_update: false,
            };
        },
        computed: {
            
        },
        components: {
        },
        ready: function() {
            this.addJQueryPlugins();
        },
        methods: {
            deleteImage: function(image) {
                var self = this;
                jQuery.ajax(this.api_content_file_delete, {
                    type: 'POST',
                    data: { file_id: image.id }
                }).success(function (data) {
                    self.images.$remove(image);
                });
            },
            getIDString: function() {
                var ids = [];
                for(var i=0; i < this.images.length; i++) {
                    ids.push(this.images[i].id);
                }
                return ids.join(',');
            },
            uploadError: function() {
                this.error = "Upload Error";
            },
            addFile: function(file) {
                this.error = false;
                this.images.push(file);
                this.$nextTick(function() {
                    this.addJQueryPlugins();
                });
            },
            addJQueryPlugins: function() {
                var self = this,
                    list = jQuery(this.$el).find('.image-list'),
                    edit = jQuery(this.$el).find('.image-list .image .editable');

                if (list.hasClass('ui-sortable')) {
                    list.sortable("destroy");
                    list.find("li").removeClass('ui-state-default');
                    list.find("li span").remove();
                }

                list.sortable({
                    stop: function( event, ui ) {
                        var ids = list.sortable("toArray", {attribute: "data-id"}),
                            imagesById = {};

                        for(var i=0; i < self.images.length; i++) {
                            imagesById[self.images[i].id] = self.images[i];
                        }

                        self.images = jQuery.map(ids, function(v, k) {
                            return imagesById[parseInt(v)];
                        });
                    }
                });

                // start editable
                edit.editable('destroy');
                edit.editable({
                    url: this.api_content_file_attribute_update,
                });
                // end editable
            }

        }
    };
</script>
