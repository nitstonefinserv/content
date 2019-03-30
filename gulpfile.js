var elixir = require('laravel-elixir'),
	gulp = require('gulp'),
	rm = require('gulp-rm'),
	exec = require('child_process').exec,
	config = {
		styles : {
			files: [
				"reflexions-pixeladmin/html/assets/stylesheets/bootstrap.css",
				"reflexions-pixeladmin/html/assets/stylesheets/pixel-admin.css",
				"reflexions-pixeladmin/html/assets/stylesheets/widgets.css",
				"reflexions-pixeladmin/html/assets/stylesheets/pages.css",
				"reflexions-pixeladmin/html/assets/stylesheets/rtl.css",
				"reflexions-pixeladmin/html/assets/stylesheets/themes.css",
				"sweetalert2/dist/sweetalert2.css",
			],
			output: "dist/css/vendor.css",
			basedir: "./bower_components/"
		},
		scripts : {
			files: [
				"jquery/dist/jquery.js",
				"reflexions-pixeladmin/html/assets/javascripts/bootstrap.js",
				"reflexions-pixeladmin/html/assets/javascripts/pixel-admin.js",
				"ckeditor/ckeditor.js",
				"vue/dist/vue.js",
				"vue-strap/dist/vue-strap.js",
				"blueimp-file-upload/js/jquery.iframe-transport.js",
				"blueimp-file-upload/js/jquery.fileupload.js",
				"responsive-bootstrap-toolkit/dist/bootstrap-toolkit.js",
				"sweetalert2/dist/sweetalert2.min.js",
			],
			output: "dist/js/vendor.js",
			basedir: "./bower_components/"
		},
		copy : [
			{
				from: "bower_components/reflexions-pixeladmin/html/assets/fonts",
				to: "dist/fonts"
			},
			{
				from: "bower_components/reflexions-pixeladmin/html/assets/images",
				to: "dist/images"
			},
			{
				from: "bower_components/ckeditor",
				to: "dist/ckeditor"
			},
			{
				from: "resources/assets/js/ckeditor/plugins",
				to: "dist/ckeditor/plugins"
			},
			{
				from: "resources/assets/js/ckeditor/ckeditor_config.js",
				to: "dist/ckeditor_config.js"
			}
		],
		sass: {
			files: [ "admin.scss" ],
			output: "dist/css/admin.css"
		},
		vueify: {
			files: [ "admin.js" ],
			output: "dist/js/admin.js"
		},
		version: {
			files: [
				'css/vendor.css',
				'css/admin.css',
				'js/vendor.js',
				'js/admin.js'
			],
			buildPath: 'dist'
		},
		browserSync: {
			proxy: '192.168.99.100'
		}
	};

elixir.config.publicPath = 'dist';
elixir.config.css.sass.pluginOptions.outputStyle = 'compact';

require('laravel-elixir-vueify');

gulp.task('parent:publish', function (cb) {
  exec('rm -Rf ../../../public/vendor/content && php ../../../artisan vendor:publish', function (err, stdout, stderr) {
    console.log(stdout);
    console.log(stderr);
    cb(err);
  });
});

elixir(function(mix) {
	mix.styles(config.styles.files, config.styles.output, config.styles.basedir)
	mix.scripts(config.scripts.files, config.scripts.output, config.scripts.basedir);
	config.copy.forEach( function(c) {
		mix.copy(c.from, c.to);
	});
	mix.sass(config.sass.files, config.sass.output);
	mix.browserify(config.vueify.files, config.vueify.output);

	// Cache busting and Documentation
	mix.version(config.version.files, config.version.buildPath)
		.task('parent:publish', 'dist/rev-manifest.json');
});
