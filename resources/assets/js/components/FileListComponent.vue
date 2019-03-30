<template>
    <div class="file-list-component">
        <div class="file-list">
            <div class="file" v-for="file in files" v-bind:data-id="file.id">
                <button type="button" class="close" v-on:click='deleteFile(file)' aria-label="Delete"><span>&times;</span></button>
                <h5>
                    <a class="editable" data-type="text" data-name="name" data-title="Enter Name" v-bind:data-pk="file.id">{{file.name}}</a>
                    <a v-bind:href="file.url" target="_blank"><span class="fa fa-download"></span></a>
                </h5>
                <p><a class="editable"
                    data-type="textarea"
                    data-name="description"
                    data-title="Enter Description"
                    data-placeholder="Description"
                    v-bind:data-pk="file.id">{{file.description}}</a></p>
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
.file-list-component{
    .file {
        h5 {
            overflow:hidden;
            line-height: 1.5;
            margin-top:0;
            .editable {
                margin-top:0;
            }
        }
        .close {
            margin-top:0;
        }

        border: 1px solid #ddd;
        border-radius: 2px;
        width: 100%;
        margin: 5px;
        padding: 4px;
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
                files: [],
                label: false,
                attribute: false,
                error: false,
                upload_url: false,
                upload_max: false,
                upload_max_bytes: false,
                upload_relative_path_prefix: false,
                upload_url_prefix: false,
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
            deleteFile: function(file) {
                var self = this;
                jQuery.ajax(this.api_content_file_delete, {
                    type: 'POST',
                    data: { file_id: file.id }
                }).success(function (data) {
                    self.files.$remove(file);
                });
            },
            getIDString: function() {
                var ids = [];
                for(var i=0; i < this.files.length; i++) {
                    ids.push(this.files[i].id);
                }
                return ids.join(',');
            },
            uploadError: function() {
                this.error = "Upload Error";
            },
            addFile: function(file) {
                this.error = false;
                this.files.push(file);
                this.$nextTick(function() {
                    this.addJQueryPlugins();
                });
            },
            addJQueryPlugins: function() {
                var self = this,
                    list = jQuery(this.$el).find('.file-list'),
                    edit = jQuery(this.$el).find('.file-list .file .editable');

                if (list.hasClass('ui-sortable')) {
                    list.sortable("destroy");
                    list.find("li").removeClass('ui-state-default');
                    list.find("li span").remove();
                }

                list.sortable({
                    stop: function( event, ui ) {
                        var ids = list.sortable("toArray", {attribute: "data-id"}),
                            filesById = {};

                        for(var i=0; i < self.files.length; i++) {
                            filesById[self.files[i].id] = self.files[i];
                        }

                        self.files = jQuery.map(ids, function(v, k) {
                            return filesById[parseInt(v)];
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
