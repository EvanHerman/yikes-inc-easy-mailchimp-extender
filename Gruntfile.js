'use strict';
module.exports = function(grunt) {

    grunt.initConfig({

        // let us know if our JS is sound
        jshint: {
            options: {
                "bitwise": true,
                "browser": true,
                "curly": true,
                "eqeqeq": true,
                "eqnull": true,
                "es5": true,
                "esnext": true,
                "immed": true,
                "jquery": true,
                "latedef": true,
                "newcap": true,
                "noarg": true,
                "node": true,
                "strict": false,
                "trailing": true,
                "undef": true,
                "globals": {
                    "jQuery": true,
                    "alert": true
                }
            },
            all: [
                'Gruntfile.js',
                'plugin_core/admin/js/*.js',
				'plugin_core/public/js/*.js'
            ]
        },

        // js minification
        uglify: {
            dist: {
                files: {
					// admin scripts
                    'plugin_core/admin/js/min/yikes-inc-easy-mailchimp-dashboard-widget.min.js': [ // widget specific script
                        'plugin_core/admin/js/yikes-inc-easy-mailchimp-dashboard-widget.js'
                    ],
                    'plugin_core/admin/js/min/yikes-inc-easy-mailchimp-extender-admin.min.js': [ // global admin script
                        'plugin_core/admin/js/yikes-inc-easy-mailchimp-extender-admin.js'
                    ],
					'plugin_core/admin/js/min/yikes-inc-easy-mailchimp-extender-edit-form.min.js': [ // edit MailChimp form script
                        'plugin_core/admin/js/yikes-inc-easy-mailchimp-extender-edit-form.js'
                    ],
					'plugin_core/admin/js/min/yikes-inc-easy-mailchimp-tinymce-button.min.js': [ // custom tinyMCE button script
                        'plugin_core/admin/js/yikes-inc-easy-mailchimp-tinymce-button.js'
                    ],
					// public scripts
					'plugin_core/public/js/yikes-mc-ajax-forms.min.js': [ // public ajax script
                        'plugin_core/public/js/yikes-mc-ajax-forms.js'
                    ],
                }
            }
        },

		// css minify all contents of our directory and add .min.css extension
		cssmin: {
			target: {
				admin_files: [
					// admin css files
					{
						expand: true,
						cwd: 'plugin_core/admin/css',
						src: ['*.css'], // main style declaration file
						dest: 'plugin_core/admin/css',
						ext: '.min.css'
					},
					{
						expand: true,
						cwd: 'public/css',
						src: ['*.css'], // global public facing styles
						dest: 'public/css',
						ext: '.min.css'
					},
				]
			}
		},

        // watch our project for changes
       watch: {
			admin_css: { // admin css
				files: 'plugin_core/admin/css/*.css',
				tasks: ['cssmin'],
				options: {
					spawn:false,
					event:['all']
				},
			},
			admin_js: { // admin js
				files: 'plugin_core/admin/js/*.js',
				tasks: ['uglify'],
				options: {
					spawn:false,
					event:['all']
				},
			},
			public_css: {
			 // public css
				files: 'plugin_core/public/css/*.css',
				tasks: ['cssmin'],
				options: {
					spawn:false,
					event:['all']
				},
			},
			public_js: { // public js
				files: 'plugin_core/public/js/*.js',
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
				src : [ 'plugin_core/admin/css/*.css' , 'plugin_core/public/css/*.css' , 'plugin_core/admin/js/*.js' , 'plugin_core/public/js/*.js' ],
			},
			options: {
				proxy : 'localhost/yikes-mailchimp',
				watchTask : true
			}
		},
		
		// Autoprefixer for our CSS files
		postcss: {
			options: {
                map: true,
                processors: [
                    require('autoprefixer-core')({
                        browsers: ['last 2 versions']
                    })
                ]
            },
			dist: {
			  src: [ 'plugin_core/admin/css/*.css' , 'plugin_core/public/css/*.css' ]
			}
		},
		  
		// make POT file
		makepot: {
	        target: {
	            options: {
	                domainPath: '/plugin_core/languages/',    // Where to save the POT file.
	                potFilename: 'yikes-inc-easy-mailchimp-extender.pot',   // Name of the POT file.
	                type: 'wp-plugin',  // Type of project
	            }
	        }
	    },
		
    });

    // load tasks
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-browser-sync'); // browser-sync auto refresh
	grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser autoprefixes)
	grunt.loadNpmTasks( 'grunt-wp-i18n' ); // wordpress localization plugin

    // register task
    grunt.registerTask('default', [
        'jshint',
        'cssmin',
        'uglify',
        'watch',
		'postcss',
		'makepot',
    ]);

};