CKEDITOR.dialog.add( 'customImageDialog', function( editor ) {
    return {
        title: 'Abbreviation Properties',
        minWidth: 400,
        minHeight: 200,

        contents: [],
        onLoad: function() {
            Admin.openMediaModal();

            // var dialog = this;

            // // var result = editor.document.createElement( 'div' );
            
            // var image = editor.document.createElement( 'img' );
            // image.setAttribute( 'src', dialog.getValueOf( 'tab-basic', 'link' ) );
            // // result.append(image);

            // var caption = editor.document.createElement( 'div' );
            // caption.setAttribute( 'class', "caption-text" );
            // caption.setText( dialog.getValueOf( 'tab-basic', 'caption' ) + " " );
            
            // var source = editor.document.createElement( 'div' );
            // source.setAttribute( 'class', "caption-author" );
            // source.setAttribute( 'style', "display:inline" );
            // source.setText( "Photo: " + dialog.getValueOf( 'tab-basic', 'source' ) );

            // caption.append(source);
            // // result.append(caption);

            // editor.insertElement( image );
            // editor.insertElement( caption );
        }
    };
});