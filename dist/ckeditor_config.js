/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.stylesSet.add( 'content_styles', [
	// Block-level styles.
	{ name: 'Paragraph', element: 'div', attributes: { 'class': 'article__paragraph' } },
	{ name: 'Intro', element: 'div', attributes: { 'class': 'article__intro' } },
	{ name: 'Opening', element: 'div', attributes: { 'class': 'article__paragraph opening' } },
	{ name: 'Quote Simple', element: 'div', attributes: { 'class': 'article__paragraph quote__simple' } },
	{ name: 'Quote Large', element: 'div', attributes: { 'class': 'article__paragraph quote__large' } }

]);

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.baseHref = '/vendor/content/ckeditor/';
	// config.stylesSet = 'content_styles';
	config.extraPlugins = 'ReflexionsContentImage,colorbutton,colordialog,font,image2';
	config.contentsCss = '/css/app.css';
	config.height = 700;
	config.allowedContent = true;
	config.toolbar = [
		['Source', '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
		['Undo', 'Redo'],
		['Styles', 'Format', 'Font', 'FontSize', 'Maximize', 'SpellChecker', 'Scayt'],
		'/',
		['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'],
		['TextColor', 'BGColor'],
		['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'],
		['Link', 'Unlink', 'Anchor' ],
		['Table', 'HorizontalRule', 'SpecialChar', 'Image']
	];

	config.removePlugins = 'elementspath,iframe,about';
	config.image_prefillDimensions = false;
	// config.skin = 'moono';

	// // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
	// config.toolbar = [
	//     { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates', '-', 'newImage' ] },
	//     { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
	//     { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	//     { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	//     '/',
	//     { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	//     { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
	//     { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	//     { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
	//     '/',
	//     { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	//     { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	//     { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
	//     { name: 'others', items: [ '-' ] },
	//     { name: 'about', items: [ 'About' ] }
	// ];

	// // Toolbar groups configuration.
	// config.toolbarGroups = [
	//     { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
	//     { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
	//     { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
	//     { name: 'forms' },
	//     '/',
	//     { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	//     { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	//     { name: 'links' },
	//     { name: 'insert' },
	//     '/',
	//     { name: 'styles' },
	//     { name: 'myDialog' },
	//     { name: 'colors' },
	//     { name: 'tools' },
	//     { name: 'others' },
	//     { name: 'about' }
	// ];
};
