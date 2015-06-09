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
                'admin/js/*.js',
				'public/js/*.js'
            ]
        },

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
				admin_files: [
					// admin css files
					{
						expand: true,
						cwd: 'admin/css',
						src: ['*.css'], // main style declaration file
						dest: 'admin/css',
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
				src : [ 'admin/css/*.css' , 'public/css/*.css' , 'admin/js/*.js' , 'public/js/*.js' ],
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
			  src: [ 'admin/css/*.css' , 'public/css/*.css' ]
			}
		},
		  
		// make POT file
		makepot: {
	        target: {
	            options: {
	                domainPath: '/languages/',    // Where to save the POT file.
	                potFilename: 'yikes-inc-easy-mailchimp-extender.pot',   // Name of the POT file.
	                type: 'wp-plugin',  // Type of project
	            }
	        }
	    },
		
		auto_install: { 
			local: {}
		},
		
    });

    // load tasks
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-browser-sync'); // browser-sync auto refresh
	grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser auto pre-fixes)
	grunt.loadNpmTasks('grunt-wp-i18n'); // wordpress localization plugin
	grunt.loadNpmTasks('grunt-auto-install'); // autoload all of ourd ependencies (ideally, you install this one package, and run grunt auto_install to install our dependencies automagically)

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