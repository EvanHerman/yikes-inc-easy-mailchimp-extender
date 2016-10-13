'use strict';
module.exports = function(grunt) {

	grunt.initConfig({

		// js minification
		uglify: {
			dist: {
				files: {
					// admin scripts
					'admin/js/min/yikes-inc-easy-mailchimp-dashboard-widget.min.js': [ // widget specific script
						'admin/js/yikes-inc-easy-mailchimp-dashboard-widget.js'
					],
					'admin/js/min/yikes-inc-easy-mailchimp-extender-admin.min.js': [ // global admin script
						'admin/js/yikes-inc-easy-mailchimp-extender-admin.js'
					],
					'admin/js/min/yikes-inc-easy-mailchimp-extender-edit-form.min.js': [ // edit MailChimp form script
						'admin/js/yikes-inc-easy-mailchimp-extender-edit-form.js'
					],
					'admin/js/min/yikes-inc-easy-mailchimp-tinymce-button.min.js': [ // custom tinyMCE button script
						'admin/js/yikes-inc-easy-mailchimp-tinymce-button.js'
					],
					// public scripts
					'public/js/yikes-mc-ajax-forms.min.js': [ // public ajax script
						'public/js/yikes-mc-ajax-forms.js'
					],
				}
			}
		},

		// css minify all contents of our directory and add .min.css extension
		cssmin: {
			target: {
				files: [
					// admin css files
					{
						expand: true,
						cwd: 'admin/css',
						src: [
							'yikes-inc-easy-mailchimp-extender-admin.css',
							'yikes-inc-easy-mailchimp-migrate-option-styles.css',
						], // main style declaration file
						dest: 'admin/css',
						ext: '.min.css'
					},
					{
						expand: true,
						cwd: 'public/css',
						src: [
							'yikes-inc-easy-mailchimp-checkbox-integration.css',
							'yikes-inc-easy-mailchimp-datepicker-styles.css',
							'yikes-inc-easy-mailchimp-extender-public.css',
						], // main style declaration file
						dest: 'public/css',
						ext: '.min.css'
					}
				]
			}
		},

		// watch our project for changes
		watch: {
			admin_css: { // admin css
				files: 'admin/css/*.css',
				tasks: ['cssmin'],
				options: {
					spawn:false,
					event:['all']
				},
			},
			admin_js: { // admin js
				files: 'admin/js/*.js',
				tasks: ['uglify'],
				options: {
					spawn:false,
					event:['all']
				},
			},
			public_css: {
			 // public css
				files: 'public/css/*.css',
				tasks: ['cssmin'],
				options: {
					spawn:false,
					event:['all']
				},
			},
			public_js: { // public js
				files: 'public/js/*.js',
				tasks: ['uglify'],
				options: {
					spawn:false,
					event:['all']
				},
			},
		},

		// Borwser Sync
		browserSync: {
			bsFiles: {
				src : [ 'admin/css/*.min.css' , 'public/css/*.min.css' , 'admin/js/*.min.js' , 'public/js/*.min.js' ],
			},
			options: {
				proxy: "localhost/mc_free/",
				watchTask : true
			}
		},

		// Autoprefixer for our CSS files
		postcss: {
			options: {
				map: true,
				processors: [
					require( 'autoprefixer-core' ) ({
						browsers: ['last 2 versions']
					})
				]
			},
			dist: {
				src: [ 'admin/css/*.css' , 'public/css/*.css' ]
			}
		},

		auto_install: {
			local: {}
		},

		/* Delete the error log - gets recreated in the next step */
		clean: ['includes/error_log/yikes-easy-mailchimp-error-log.php' ],

	});

	// load tasks
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-browser-sync'); // browser-sync auto refresh
	grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser auto pre-fixes)
	grunt.loadNpmTasks('grunt-wp-i18n'); // wordpress localization plugin
	grunt.loadNpmTasks('grunt-auto-install'); // autoload all of our dependencies (ideally, you install this one package, and run grunt auto_install to install our dependencies automagically)
	grunt.loadNpmTasks('grunt-contrib-clean');

	// register task
	grunt.registerTask( 'default', [
			'uglify',
			'cssmin',
			'postcss',
			'clean',
			'emptyFile'
	]);

	grunt.registerTask( 'emptyFile', 'Creates an empty file', function() {
		grunt.file.write('includes/error_log/yikes-easy-mailchimp-error-log.php', '');
	});

};
