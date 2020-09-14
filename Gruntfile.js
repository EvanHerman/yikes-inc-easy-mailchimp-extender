'use strict';
module.exports = function(grunt) {

	grunt.initConfig({
		uglify: {
			dist: {
				files: {
					'admin/js/min/yikes-inc-easy-mailchimp-dashboard-widget.min.js': [
						'admin/js/yikes-inc-easy-mailchimp-dashboard-widget.js'
					],
					'admin/js/min/yikes-inc-easy-mailchimp-extender-admin.min.js': [
						'admin/js/yikes-inc-easy-mailchimp-extender-admin.js'
					],
					'admin/js/min/yikes-inc-easy-mailchimp-extender-edit-form.min.js': [
						'admin/js/yikes-inc-easy-mailchimp-extender-edit-form.js'
					],
					'admin/js/min/yikes-inc-easy-mailchimp-tinymce-button.min.js': [
						'admin/js/yikes-inc-easy-mailchimp-tinymce-button.js'
					],
					'public/js/yikes-mc-ajax-forms.min.js': [
						'public/js/yikes-mc-ajax-forms.js'
					],
					'public/js/yikes-datepicker-scripts.min.js': [
						'public/js/yikes-datepicker-scripts.js'
					],
					'public/js/form-submission-helpers.min.js': [
						'public/js/form-submission-helpers.js'
					],
				}
			}
		},
		cssmin: {
			target: {
				files: [
					{
						expand: true,
						cwd: 'admin/css',
						src: [
							'yikes-inc-easy-mailchimp-extender-admin.css',
						],
						dest: 'admin/css',
						ext: '.min.css'
					},
					{
						expand: true,
						cwd: 'public/css',
						src: [
							'yikes-inc-easy-mailchimp-datepicker-styles.css',
							'yikes-inc-easy-mailchimp-extender-public.css',
						],
						dest: 'public/css',
						ext: '.min.css'
					}
				]
			}
		},
		pot: {
			options: {
				text_domain: 'yikes-inc-easy-mailchimp-extender', 
				dest: 'languages/', 
		        keywords: [
		        	'__:1',
		        	'_e:1',
					'_x:1,2c',
					'esc_html__:1',
					'esc_html_e:1',
					'esc_html_x:1,2c',
					'esc_attr__:1', 
					'esc_attr_e:1', 
					'esc_attr_x:1,2c', 
					'_ex:1,2c',
					'_n:1,2', 
					'_nx:1,2,4c',
					'_n_noop:1,2',
					'_nx_noop:1,2,3c'
				],
			},
			files: {
				src:  [ '**/*.php' ],
				expand: true,
			}
		}

	});

	// Load tasks.
	grunt.loadNpmTasks( 'grunt-contrib-uglify-es' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-pot' );

	// Register task.
	grunt.registerTask( 'default', [
		'uglify',
		'cssmin'
	]);
	grunt.registerTask( 'css', [ 'cssmin' ]);

};
