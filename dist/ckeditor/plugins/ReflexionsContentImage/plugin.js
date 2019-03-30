CKEDITOR.plugins.add( 'ReflexionsContentImage', {
    icons: 'ReflexionsContentImage',
    init: function( editor ) {
        editor.addCommand( 'ReflexionsContentImage',
            {
                exec: function( editor ) {
                    var el = jQuery('<div></div>'),
                        browser = null;
                    jQuery('body').append(el);
                    browser = new ContentModalImageBrowser({
                        'el': el[0],
                        data: {
                            'label' : editor.config.reflexions.label,
                            'upload_url' : editor.config.reflexions.upload_url,
                            'upload_max' : editor.config.reflexions.upload_max,
                            'upload_max_bytes' : editor.config.reflexions.upload_max_bytes,
                            'upload_relative_path_prefix' : editor.config.reflexions.upload_relative_path_prefix,
                            'upload_url_prefix' : editor.config.reflexions.upload_url_prefix,
                            'api_content_images_lookup' : editor.config.reflexions.api_content_images_lookup,
                            'api_content_file_delete' : editor.config.reflexions.api_content_file_delete,
                        }
                    });
                    browser.$on('select-image', function(image) {
                        var container = editor.document.createElement( 'div' );
                        container.setAttribute( 'class', 'article-image-container' );
                        container.setAttribute( 'title', image.name );

                        var img = editor.document.createElement( 'img' );
                        img.setAttribute( 'src', image.url );
                        img.setAttribute( 'class', "article-image" );
                        container.append(img); 

                        if (image.description) {
                            var caption = editor.document.createElement( 'caption' );
                            caption.setText( image.description );
                            container.append(caption);                            
                        }

                        editor.insertElement( container );
                        browser.$remove();
                        el.remove();
                    });
                    browser.openSelectImageBrowser();
                }
            }
         );

        CKEDITOR.dialog.add( 'ReflexionsContentImageDialog', this.path + 'dialogs/customImage.js' );
        editor.ui.addButton( 'ReflexionsContentImage', {
            label: 'Insert Image',
            command: 'ReflexionsContentImage',
            toolbar: 'insert'
        });
    }
});
