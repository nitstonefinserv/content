<template>
    <div class="upload-button">
        <span class="btn btn-primary btn-upload">
            <i class="fa fa-plus"></i>
            <span>Upload</span>
            <!-- The file input field used as target for the file upload widget -->
            <input class="fileupload" type="file" name="upload">
        </span>
    </div>
</template>


<style lang="sass">
.upload-button {
    .btn-upload {
        position: relative;
        overflow: hidden;
    }
    input.fileupload { 
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }
    display:inline;
}
</style>

<script>
    export default {
        props: [
            'attribute',
            'upload_url',
            'upload_max',
            'upload_max_bytes',
            'upload_relative_path_prefix',
            'upload_url_prefix',
        ],
        ready: function() {
            // start file upload
            var self = this,
                input = jQuery(this.$el).find('input.fileupload'),
                parent = input.parent();
            input.fileupload({
                url: self.upload_url,
                dataType: 'json',
                maxFileSize: self.upload_max_bytes,
                formData: {
                    relative_path_prefix: self.upload_relative_path_prefix,
                    upload_url_prefix: self.upload_url_prefix,
                    group: self.attribute
                },
                start: function (e, data) {
                    parent.addClass('disabled');
                    parent.find('.fa')
                        .removeClass('fa-plus')
                        .addClass('fa-refresh')
                        .addClass('fa-spin');
                    self.$emit('start');
                },
                fail: function (e, data) {
                    parent.removeClass('disabled');
                    parent.find('.fa')
                        .addClass('fa-plus')
                        .removeClass('fa-refresh')
                        .removeClass('fa-spin');
                    self.$emit('fail');
                },
                done: function (e, data) {
                    parent.removeClass('disabled');
                    parent.find('.fa')
                        .addClass('fa-plus')
                        .removeClass('fa-refresh')
                        .removeClass('fa-spin');
                    self.$emit('done', data.result.file);
                }
            });
            input.prop('disabled', !jQuery.support.fileInput);
            parent.addClass(jQuery.support.fileInput ? undefined : 'disabled');
            // end file upload
        }
    };
</script>
