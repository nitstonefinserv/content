<template>
    <div class="content-files-grid">
        <div class="row" v-for="row in rows">
            <div class="{{ column_class }} file" v-for="file in row">
                <button type="button" class="close" v-on:click='deleteFile(file)' aria-label="Delete"><span>&times;</span></button>
                <h5>
                    {{file.name}}
                    <a v-bind:href="file.url" target="_blank"><span class="fa fa-download"></span></a>
                </h5>
                <p>{{file.description}}</a></p>
                <p><a  v-on:click="selectFile(file)" class="btn btn-default btn-block" role="button">Select</a></p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                files: [],
                columns_per_row: 3,
                column_class: 'col-md-4'
            };
        },

        props: [
            'api_content_files_lookup',
            'api_content_file_delete'
        ],

        ready: function() {
            var self = this;
            if (global.ResponsiveBootstrapToolkit && global.jQuery) {
                self.setColumnSize(global.ResponsiveBootstrapToolkit);
                global.jQuery(global).resize(function() {
                    self.setColumnSize(global.ResponsiveBootstrapToolkit);
                });
            }

            self.$on('load', function () {
                  self.load();
            });
        },
        computed: {
            rows: function() {
                var rows = [];
                var current_row = [];
                for (var i=0; i< this.files.length;i++) {
                    current_row.push(this.files[i]);
                    if (current_row.length == this.columns_per_row) {
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
            setColumnSize: function (viewport) {
                // Executes only in XS breakpoint
                if(viewport.is('xs')) {
                    this.column_class = 'col-xs-12';
                    this.columns_per_row = 1;
                }

                // Executes in SM, MD breakpoints
                if(viewport.is('sm') || viewport.is('md') ) {
                    this.column_class = 'col-sm-4';
                    this.columns_per_row = 3;
                }

                // Executes in LG breakpoints
                if(viewport.is('>md')) {
                    this.column_class = 'col-lg-2';
                    this.columns_per_row = 6;
                }
            },
            load: function() {
                var self = this;
                jQuery.ajax(this.api_content_files_lookup, {
                    type: 'GET',
                    data: {  }
                }).success(function (data) {
                    self.files = data.files;
                    self.$emit('loaded');
                });
            },
            selectFile: function(file) {
                this.$emit('selected', file);
            },
            deleteFile: function(file) {
                var self = this;
                jQuery.ajax(this.api_content_file_delete, {
                    type: 'POST',
                    data: { file_id: file.id }
                }).success(function (data) {
                    self.files.$remove(file);
                    self.$emit('deleted', file);
                });
            },
            addFile: function(file) {
                this.files.push(file);
            }
        }
    };
</script>

<style lang="sass">
.content-files-grid {
    .file {
        h5 {
            overflow:hidden;
            line-height: 1.5;
        }
        .close {
            margin-top: 8px;
        }
        border: 1px solid #ddd;
        border-radius: 2px;
        margin: 5px;
        padding: 4px;
    }
}
</style>
