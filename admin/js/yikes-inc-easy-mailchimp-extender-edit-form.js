window.Yikes_Mailchimp_Edit_Form = window.Yikes_Mailchimp_Edit_Form || {};
window.yikes_mailchimp_edit_form = window.yikes_mailchimp_edit_form || {};

(function( window, document, $, app, undefined ) {
	'use strict';

	app.l10n = window.yikes_mailchimp_edit_form || {};

	 $( document ).ready( function() {

	 	// Show tags added to this form.
	 	add_tags_to_form_onload();

	 	// On page load, check if there are any fields in the form builder
	 	// If we find fields, show the field instructions
	 	// If we don't find fields, hide the field instructions
	 	if ( jQuery( '#form-builder-container > section' ).length === 0 ) {
	 		jQuery( '.edit-form-description-form-builder' ).hide();
	 	}

		/* Initialize Sortable Container */
		/* Sortable Form Builder - re-arrange field order (edit-form.php) */
		$( 'body' ).find( '#form-builder-container' ).sortable({
			items: '.draggable:not(.non-draggable-yikes)',
			axis: 'y',
			placeholder: 'form-builder-placeholder',
			update: function( ) {
			  var i = 1;
			  jQuery( '#form-builder-container' ).find( '.draggable' ).each( function() {
					jQuery( this ).find( '.position-input' ).val( i );
					i++;
			  });
			}
		});

		/*
		* Remove a field from the form builder
		* re-enable it in the available fields list
		*/
		$( 'body' ).on( 'click' , '.remove-field' , function() {
			var merge_tag = jQuery( this ).attr( 'alt' );
			var clicked = jQuery( this );
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).prev().find( '.yikes-mc-expansion-toggle' ).toggleClass( 'dashicons-minus' );
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).slideToggle( 450 , function() {
				clicked.parents( '.draggable' ).find( '.expansion-section-title' ).css( 'background' , 'rgb(255, 134, 134)' );
				clicked.parents( '.draggable' ).fadeOut( 'slow' , function() {
					/* re-enable the field, to be added to the form */
					jQuery( '#available-fields' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
					jQuery( '#available-interest-groups' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
					/* remove the element from the DOM */
					jQuery( this ).remove();
					if( $( '#form-builder-container' ).find( '.draggable' ).length < 1 ) {
						$( '.clear-form-fields' ).hide();
						$( '.clear-form-fields' ).next().hide(); /* Update Form button next to clear form fields */
						$( '#form-builder-container' ).html( '<h4 class="no-fields-assigned-notice non-draggable-yikes"><em>'+app.l10n.no_fields_assigned+'</em></h4>' );
					}
				});
			});
			return false;
		});

		/*
		* Hide a [newly-added/unsaved] field (click 'close')
		*/
		$( 'body' ).on( 'click' , '.hide-field' , function() {
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).slideToggle( 450 ).prev().find( '.yikes-mc-expansion-toggle' ).toggleClass( 'dashicons-minus' );
			return false;
		});

		/*
		* Send selected field to the form builder
		* and disable it from the available fields list
		*/
		$( 'body' ).on( 'click' , '.add-field-to-editor' , function() {

			// Display our form instructions (we hide these if there are no fields)
			jQuery( '.edit-form-description-form-builder' ).show();
			const clicked_button = $( this );

			$( '#available-fields .add-to-form' ).each( function() {
				/* get the length, to decide if we should clear the html and append, or just append */
				var form_builder_length = $( '#form-builder-container' ).find( '.draggable' ).length;

				var field = $( this );
				var merge_tag = field.attr( 'alt' );

				/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
				$( '#available-fields li' ).removeClass( 'available-field' );
				clicked_button.attr( 'disabled' , 'disabled' ).attr( 'onclick' , 'return false;' ).removeClass( 'add-field-to-editor' );

				/* build our data */
				var data = {
					'action'     : 'add_field_to_form',
					'field_name' : field.attr( 'data-attr-field-name' ),
					'merge_tag'  : merge_tag,
					'field_type' : field.attr( 'data-attr-field-type' ),
					'list_id'    : get_list_id()
				};

				/* submit our ajax request */
				$.ajax({
					url: app.l10n.ajax_url,
					type:'POST',
					data: data,
					dataType: 'html',
					success : function( response, textStatus, jqXHR) {
						field.removeClass( 'add-to-form' ).addClass( 'not-available' );

						/* If the banner is visible, this means that there is no fields assigned to the form - clear it */
						if ( $( '.no-fields-assigned-notice' ).is( ':visible') ) {
							$( '#form-builder-container' ).html( '' );
						}

						/* Append our response, and display our buttons */
						$( '#form-builder-container' ).append( response );
						$( '.clear-form-fields' ).show(); /* Clear Form Fields */
						$( '.clear-form-fields' ).next().show(); /* Update Form button next to clear form fields */

						/* add a value to the position */
						$( '.field-'+merge_tag+'-position' ).val( parseInt( form_builder_length + 1 ) ); /* add one :) */
					},
					error : function( jqXHR, textStatus, errorThrown ) {
						alert( textStatus+jqXHR.status+jqXHR.responseText+"..." );
					},
					complete : function( jqXHR, textStatus ) {
						/* console.log( 'field successfully added to the form' ); */
						/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
						$( '#available-fields li' ).addClass( 'available-field' );
						clicked_button.fadeOut( 'fast', function() {
							clicked_button.removeAttr( 'disabled' ).removeAttr( 'onclick' ).addClass( 'add-field-to-editor' ).fadeOut();
						});
					}
				});
			});
			return false;
		}); /* end add field to form builder */

		/*
		* Send selected Interest group to our form
		* and disable it from the available interest groups list
		*/
		$( 'body' ).on( 'click' , '.add-interest-group-to-editor' , function() {
			/* get the length, to decide if we should clear the html and append, or just append */
			var form_builder_length = $( '#form-builder-container' ).find( '.draggable' ).length;

			var interest_groups = [];
			$( '#available-interest-groups .add-to-form' ).each( function() {
				interest_groups.push({
					'group_id'  : $( this ).attr( 'alt' ),
					'field_type': $( this ).attr( 'data-attr-field-type' ),
					'field_name': $( this ).attr( 'data-attr-field-name' )
				});
			});

			/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
			$( '#available-interest-groups li' ).removeClass( 'available-field' );

			var button = $( this );
			button.attr( 'disabled' , 'disabled' ).attr( 'onclick' , 'return false;' ).removeClass( 'add-interest-group-to-editor' );

			/* build our data */
			var data = {
				'action' : 'add_interest_group_to_form',
				'interest_groups': interest_groups,
				'list_id' : get_list_id()
			};

			/* submit our ajax request */
			$.ajax({
				url: app.l10n.ajax_url,
				type:'POST',
				data: data,
				dataType: 'html',
				success : function( response, textStatus, jqXHR) {
					$( '#available-interest-groups .add-to-form' ).removeClass( 'add-to-form' ).addClass( 'not-available' );
					if ( form_builder_length < 1 ) {
						$( '#form-builder-container' ).html( '' ).append( response );
						$( '.clear-form-fields' ).show();
						$( '.clear-form-fields' ).next().show(); /* Update Form button next to clear form fields */
					} else {
						$( '#form-builder-container' ).append( response );
					}
				},
				error : function( jqXHR, textStatus, errorThrown ) {
					alert( textStatus+jqXHR.status+jqXHR.responseText+"..." );
				},
				complete : function( jqXHR, textStatus ) {
					/* console.log( 'interest group successfully added to the form..' ); */
					/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
					$( '#available-interest-groups' ).children( 'li' ).addClass( 'available-field' );
					button.fadeOut( 'fast', function() {
						button.removeAttr( 'disabled' ).removeAttr( 'onclick' ).addClass( 'add-interest-group-to-editor' );
					});
				}
			});
			return false;
		}); /* end add field to form builder */

		$( 'body' ).on( 'click' , '.add-tag-to-editor' , function() {
			const elem  = $( this );
			const tags  = [];
			const items = $( '#available-tags li' );
			$( '#available-tags .add-to-form' ).each( function() {
				const tag = $( this );
				tags.push({
					'tag_name': tag.data( 'tag-name' ),
					'tag_id'  : tag.data( 'tag-id' ),
				});
			});

			// Disable ability to add tags to the form while AJAX is running.
			items.removeClass( 'available-field' );
			elem.attr( 'disabled' , 'disabled' ).removeClass( 'add-tag-to-editor' );

			const data = {
				'action' : 'add_tag_to_form',
				'tags'   : tags,
				'list_id': get_list_id(),
				'form_id': get_form_id(),
				'nonce'  : app.l10n.add_tag_nonce
			};

			// Ajax.
			$.ajax({
				url : app.l10n.ajax_url,
				type: 'POST',
				data: data,
				success: function( response, textStatus, jqXHR ) {
					$( '#available-tags .add-to-form' ).removeClass( 'add-to-form' ).addClass( 'not-available' );
					add_tags_to_form( response.data.tags );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log( textStatus );
					console.log( jqXHR.status );
					console.log( jqXHR.responseText );
				},
				complete: function( jqXHR, textStatus ) {

					// Enable ability to add the tags to the form.
					items.addClass( 'available-field' );
					elem.removeAttr( 'disabled' ).addClass( 'add-tag-to-editor' ).hide();
				}
			});

			return false;
		});

		// Remove tag.
		$( 'body' ).on( 'click' , '.mailchimp-tag .dashicons.dashicons-no-alt', function() {
			// Prevent any other tag from being removed during the process.
			$( '.mailchimp-tag' ).css( { 'pointer-events': 'none', 'opacity': '.5' } );

			const icon   = $( this );
			const elem   = icon.parents( '.mailchimp-tag' );
			const tag_id = elem.data( 'tag-id' );
			const data   = {
				'action' : 'remove_tag_from_form',
				'tag'    : tag_id,
				'list_id': get_list_id(),
				'form_id': get_form_id(),
				'nonce'  : app.l10n.remove_tag_nonce
			};

			icon.removeClass( 'dashicons-no-alt' ).addClass( 'dashicons-trash' ).fadeOut( 550 ).fadeIn( 550 );
			const interval = setInterval( function () {
				icon.fadeOut( 550 ).fadeIn( 550 );
			}, 700 );

			// Ajax.
			$.ajax({
				url : app.l10n.ajax_url,
				type: 'POST',
				data: data,
				success: function( response, textStatus, jqXHR ) {
					$( `#tag-${ tag_id }` ).removeClass( 'not-available' ).removeAttr( 'disabled' );
					elem.fadeOut( 'slow', function(){ 
						elem.remove();
						tags_container_check(); 
						$( '.mailchimp-tag' ).css( { 'pointer-events': 'auto', 'opacity': '1.0' } );
					});
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log( textStatus );
					console.log( jqXHR.status );
					console.log( jqXHR.responseText );
				},
				complete: function( jqXHR, textStatus ) {
					clearInterval( interval );
				}
			});

			return false;
		});

		/* initialize color pickers */
		$('.color-picker').each(function() {
			$( this ).wpColorPicker();
		}); /* end color picker initialization */

		/* Toggle settings hidden containers */
		$( 'body' ).on( 'click' , '.expansion-section-title' , function() {
			$( this ).next().stop().slideToggle();
			$( this ).find( '.yikes-mc-expansion-toggle' ).toggleClass( 'dashicons-minus' );
			return false;
		});

		/* Toggle Selected Class (Available Merge Vars) */
		$( 'body' ).on( 'click' , '#container-container .available-field' , function() {
			const element = $( this );
			if ( element.hasClass( 'not-available' ) ) {
				return false;
			} else {
				element.toggleClass( 'add-to-form' );
			}

			const ul            = element.parents( 'ul' );
			const add_to_editor = ul.siblings( '.add-to-editor' );

			if ( ul.children( '.add-to-form' ).length === 0 ) {
				add_to_editor.stop().fadeOut();
			}  else {
				add_to_editor.stop().fadeIn();
			}
		});

		/* Toggle Additional Form Settings (customizer, builder, error messages) */
		$( 'body' ).on( 'click' , '.hidden_setting' , function() {
			$( '.hidden_setting' ).removeClass( 'selected_hidden_setting' );
			$( '.selected_setting_triangle' ).remove();
			$( this ).addClass( 'selected_hidden_setting' ).append( '<div class="selected_setting_triangle"></div>' );
			var container = $( this ).attr( 'data-attr-container' );
			$( '.hidden-setting-label' ).hide();
			$( '#'+container ).show();
		});

		/* Close the form when clickcing 'close' */
		$( 'body' ).on( 'click' , '.close-form-expansion' , function() {
			var expansion_section = $( this ).parents( '.yikes-mc-settings-expansion-section' ).slideToggle();
			expansion_section.prev().find( '.yikes-mc-expansion-toggle' ).toggleClass( 'dashicons-minus' );
			return false;
		});

		/* Toggle between tabs */
		$( 'body' ).on( 'click' , '.mv_ig_list .nav-tab' , function() {
			const element      = $( this );
			const prev_element = $( '.nav-tab-active' );
			if ( element.hasClass( 'nav-tab-active' ) || element.hasClass( 'nav-tab-disabled' ) ) {
				return false;
			}
			const clicked_tab_position      = parseInt( element.index() );
			prev_element.removeClass( 'nav-tab-active' );
			$( '.arrow-down' ).remove();
			element.addClass( 'nav-tab-active' ).prepend( '<div class="arrow-down"></div>' );

			const containers              = $( '#container-container .list-container' );
			const num_containers          = containers.length;
			const selected_container      = $( '#container-container .list-container:eq(' + clicked_tab_position + ')' );
			const non_selected_containers = $( '#container-container .list-container' ).not( selected_container );
			const width                   = -283;
			var left = width * clicked_tab_position;

			$.each( containers, function( index, ele ) {

				let position = parseInt( $( ele ).index() );

				if ( position !== clicked_tab_position ) {
					if ( position > clicked_tab_position ) {
						left = 0;
					} else {
						left = ( position + 1 ) * width;
					}
				}

				$( ele ).stop().animate({
					left:  left + 'px'
				});
			});
			return false;
		});

		/*
		*	Clear all fields assigned to a form in bulk
		*	@since 6.0.2.2
		*/
		$( 'body' ).on( 'click', '.clear-form-fields', function() {
			if ( confirm( app.l10n.bulk_delete_alert ) ) {
				/* hide/remove the fields */
				$( '#form-builder' ).find( '.draggable' ).find( '.expansion-section-title' ).each( function() {
					$( this ).css( 'background' , 'rgb(255, 134, 134)' );
					var merge_tag = $( this ).parents( '.draggable' ).find( '.remove-field' ).attr( 'alt' );
					$( this ).fadeOut( 'slow', function() {
						/* re-enable the field, to be added to the form */
						$( '#available-fields' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
						$( '#available-interest-groups' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
						/* hide the button */
						$( this ).remove();
						$( '.clear-form-fields' ).hide(); /* Clear form fields button */
						$( '.clear-form-fields' ).next().hide(); /* Update Form button next to clear form fields */
						$( '.available-form-field' ).each( function()  {
							$( this ).removeClass( 'not-available' );
						});
						$( '#form-builder-container' ).html( '<h4 class="no-fields-assigned-notice non-draggable-yikes"><em>'+app.l10n.no_fields_assigned+'</em></h4>' );
					});
				});
			}
			return false;
		});

		/**
		* Click the edit pencil dashicon on the expansion-section when the field is expanded
		*/
		$( '.yikes-mc-edit-field-label-icon' ).click( function( event ) {

			// Prevent the tab from sliding up
			yikes_mc_prevent_default_stop_prop( event );

			// Remove the error message
			jQuery( this ).siblings( '.yikes-mc-edit-field-label-message' ).fadeOut();

			// Store the this var
			var clicked_element = this;

			// Call our function to toggle the input field / text fields
			yikes_mc_toggle_field_label_edit( clicked_element );
		});

		/**
		* Listen for clicks on the edit field label input field and do not slide tab up/down.
		*/
		$( '.yikes-mc-edit-field-label-input' ).click( function( event ) { yikes_mc_prevent_default_stop_prop( event ) } );

		/**
		* Save field label edit changes
		*/
		$( '.yikes-mc-save-field-label-edits-icon' ).click( function( event ) {

			// Prevent the tab from sliding up
			yikes_mc_prevent_default_stop_prop( event );

			// Remove the error message
			jQuery( this ).siblings( '.yikes-mc-edit-field-label-message' ).fadeOut();

			// Store the this var
			var clicked_element = this;

			// Get the current list ID
			var list_id = get_list_id();

			// Capture the field data
			var field_name	= jQuery( this ).siblings( '.yikes-mc-edit-field-label-input' ).val();
			var field_id	= jQuery( this ).parents( '.expansion-section-title' ).siblings( '.yikes-mc-settings-expansion-section' ).children( '.yikes-mc-merge-field-id' ).val();		
			var field_data	= {
				field_name: field_name,
				field_id: field_id
			};

			// Capture the current value of the field label
			var current_field_name = jQuery( this ).parents( '.expansion-section-title' ).siblings( '.yikes-mc-settings-expansion-section' ).children( '.yikes-mc-merge-field-label' ).val();

			// Do a quick check to make sure the user is actually changing the value
			// If they're not changing the value (just using the checkmark to cancel) then just run our cancel function
			if ( current_field_name === field_name ) {
				yikes_mc_toggle_field_label_edit( jQuery( this ).siblings( '.yikes-mc-edit-field-label-icon' ) );
				return;
			}

			// Call our function to save the changes
			yikes_mc_save_field_label_name( clicked_element, list_id, field_data );
		});

		/**
		*	Initialize our date pickers on init
		*	@since 6.0.3.8
		*/
		initialize_form_schedule_time_pickers();

		// If ajax is disabled, hide the 'redirect-new-window' option
		// Is ajax is enabled and redirect is enabled, show the 'redirect-new-window' option
		$( '.yikes-enable-disable-ajax' ).click( function() {
			var is_ajax  = $( '#enable-ajax' ).is( ':checked' );
			var redirect = $( '#redirect-user' ).is( ':checked' );
			if ( is_ajax === true && redirect === true ) {
				$( '.redirect-new-window-div' ).fadeIn();	
			} else {
				$( '.redirect-new-window-div' ).fadeOut();
			}
			
		});

	});

	function get_list_id() {
		return $( '#form-builder-div' ).data( 'list-id' );
	}

	function get_form_id() {
		return $( '#form-builder-div' ).data( 'form-id' );
	}

	function add_tags_to_form_onload() {
		const data = {};
		$( '#available-tags .not-available' ).each( function( index, element ) {
			const tag_el = $( element );
			const tag_id = tag_el.data( 'tag-id' );
			const name   = tag_el.data( 'tag-name' );
			data[tag_id] = { id: tag_id, name: name };
		});
		add_tags_to_form( data );
	}

	function add_tags_to_form( tags ) {
		const tags_container = $( '.tags-title-container' );
		$.each( tags, function( tag_id, tag ) {
			const tag_html = create_tag( tag );
			tags_container.append( tag_html );
		});
		tags_container_check();
	}

	function create_tag( tag ) {
		return `<span class="mailchimp-tag" id="${ tag.id }" data-tag-name="${ tag.name }" data-tag-id="${ tag.id }">${ tag.name }<span class="tag-divider"></span><span class="dashicons dashicons-no-alt"></span></span>`;
	}

	function tags_container_check() {
		const tags_container = $( '.tags-title' );
		if ( $( '.mailchimp-tag' ).length === 0 ) {
			tags_container.slideUp();
		} else {
			tags_container.slideDown();
		}
	}


})( window, document, jQuery, Yikes_Mailchimp_Edit_Form );


/* Toggle Page Slection for form submission redirection */
function togglePageRedirection( e ) {

	var is_ajax = jQuery( '#enable-ajax' ).is( ':checked' );

	if( e.value == 1 ) {
		jQuery( '#redirect-user-to-selection-label' ).fadeIn();

		if ( is_ajax === true ) {
			jQuery( '.redirect-new-window-div' ).fadeIn();
		}

	} else {
		jQuery( '#redirect-user-to-selection-label, .redirect-new-window-div' ).fadeOut();
	}
}
/* Pass the clicked element for proper populating */
function storeGlobalClicked( e ) {
	/* get the input field name */
	var parent_name = e.parents( 'td' ).find( 'input' ).attr( 'name' );
	/* pass it to hidden thickbox field */
	jQuery( '.clicked-input' ).val( parent_name );
}
/* Populate the input field with the selected tag */
function populateDefaultValue( tag ) {
	/* store the value */
	var field = jQuery( '.clicked-input' ).val();
	/* clear input */
	jQuery( '.clicked-input' ).val( '' );
	/* remove thickbox */
	tb_remove();
	/* populate the field */
	jQuery( 'input[name="'+field+'"]' ).val( tag );
}

/**
*	toggle_nested_section()
*	- toggle the visibility of some additional options
*	@since 1.0
*/
function toggle_nested_section( clicked_option ) {
	var clicked_value = jQuery( clicked_option ).val();
	switch( clicked_value ) {

		case 'image':
			jQuery( '.submit-button-type-text' ).fadeOut( 'fast', function() {
				jQuery( '.submit-button-type-image' ).fadeIn( 'fast' );
			});
			break;

		case 'text':
			jQuery( '.submit-button-type-image' ).fadeOut( 'fast', function() {
				jQuery( '.submit-button-type-text' ).fadeIn( 'fast' );
			});
			break;

		default:
		case '1':
			/* Schedule toggle */
			if( jQuery( clicked_option ).attr( 'name' ) == 'yikes-easy-mc-form-schedule' ) {
				jQuery( '.date-restriction-section' ).fadeToggle();
			} else {
				/* login required toggle */
				jQuery( '.login-restriction-section' ).fadeToggle();
			}
			break;
	}
	return false;
}

/**
*	Initialize the date/time pickers on the scheduled section of the edit form page (form settings section)
*	@since 6.0.3.8
*/
function initialize_form_schedule_time_pickers() {
	/* Initialize the date pickers */
	jQuery( '.date-picker' ).datepicker({
		numberOfMonths: 1,
		showButtonPanel: true,
		closeText: window.yikes_mailchimp_edit_form.closeText,
		currentText: window.yikes_mailchimp_edit_form.currentText,
		monthNames: window.yikes_mailchimp_edit_form.monthNames,
		monthNamesShort: window.yikes_mailchimp_edit_form.monthNamesShort,
		dayNames: window.yikes_mailchimp_edit_form.dayNames,
		dayNamesShort: window.yikes_mailchimp_edit_form.dayNamesShort,
		dayNamesMin: window.yikes_mailchimp_edit_form.dayNamesMin,
		dateFormat: window.yikes_mailchimp_edit_form.dateFormat,
		firstDay: window.yikes_mailchimp_edit_form.firstDay,
		isRTL: window.yikes_mailchimp_edit_form.isRTL,
		onSelect: function( newDate, instance ) {

			// Prevent the browser from jumping to the top of a page when a date is selected.
			event.preventDefault();

			// Validate the date.
			var prevDate = instance.lastVal;
			var changed_object_id = instance.id;
			yikes_check_valid_date( newDate, prevDate, changed_object_id );
		},
	});
	/* initialize the time pickers */
	jQuery( '.time-picker' ).timepicker({
		scrollDefault: 'now',
		timeFormat: 'h:i A'
	});
	jQuery( '.time-picker' ).on( 'changeTime', function( event ) {
		var changed_object_id = jQuery( this ).attr( 'id' );
		var newDate = jQuery( '#yikes-easy-mc-form-restriction-start-date' ).val();
		var prevDate = jQuery( '#yikes-easy-mc-form-restriction-end-date' ).val();
		yikes_check_valid_date( newDate, prevDate, changed_object_id );
	});
}

/**
*	Check if selected date is valid, and start date is before end date
*	@since 6.0.3.8
*/
function yikes_check_valid_date( new_date, previous_date, changed_object_id ) {

	var start_date = jQuery( '#yikes-easy-mc-form-restriction-start-date' ).val();
	var start_time = yikes_12_to_24_hour_time_conversion( jQuery( '#yikes-easy-mc-form-restriction-start-time' ).val() );

	var end_date = jQuery( '#yikes-easy-mc-form-restriction-end-date' ).val();
	var end_time = yikes_12_to_24_hour_time_conversion( jQuery( '#yikes-easy-mc-form-restriction-end-time' ).val() );

	var start_date_time = new Date( start_date + ' ' + start_time );
	var end_date_time = new Date( end_date + ' ' + end_time );

	/*
	*	if the start date & time are later than the end date time,
	* 	display an error and repopulate with previous value
	*/
	if( start_date_time > end_date_time ) {
		if( changed_object_id == 'yikes-easy-mc-form-restriction-start-date' || changed_object_id == 'yikes-easy-mc-form-restriction-end-date' ) {
			/* return to previous date */
			jQuery( '#' + changed_object_id ).val( previous_date );
		}
		/* if error is present, abort */
		if( jQuery( '.date-restriction-section' ).find( 'p.description.error' ).length ) {
			return;
		}
		/* display an error message */
		jQuery( '.date-restriction-section' ).first().find( 'p.description' ).after( '<p class="description error">' + window.yikes_mailchimp_edit_form.start_date_exceeds_end_date_error + '</p>' );
	} else {
		jQuery( '.date-restriction-section' ).find( 'p.description.error' ).remove();
	}
}

function yikes_12_to_24_hour_time_conversion( time ) {

	if ( ! time ) {
		return '';
	}

    var hours = Number(time.match(/^(\d+)/)[1]);
    var minutes = Number(time.match(/:(\d+)/)[1]);
    var AMPM = time.match(/\s(.*)$/)[1];
    if (AMPM == "PM" && hours < 12) hours = hours + 12;
    if (AMPM == "AM" && hours == 12) hours = hours - 12;
    var sHours = hours.toString();
    var sMinutes = minutes.toString();
    if (hours < 10) sHours = "0" + sHours;
    if (minutes < 10) sMinutes = "0" + sMinutes;
    return (sHours + ":" + sMinutes);
}

/**
 * Toggle the visibility of the send update email container, based on the user selection
 * @param  mixed The radio button that was clicked, to read the value from
 */
function toggleUpdateEmailContainer( clicked_button ) {
	jQuery( '.send-update-email' ).stop().fadeToggle();
}

/**
* Wrapper function for event.preventDefault and event.stopPropagation
*/
function yikes_mc_prevent_default_stop_prop( event ) {
	event.preventDefault();
	event.stopPropagation();
}

/**
* Toggle the field label edit sections
*
* If you're on the normal 'Field Label Text' view: change the pencil icon to an X icon, and replace the field label/type with an input field for field-label editing
* If you're on the 'Edit Field Label' view: change the X to a pencil, and replace the input field with the field label name and type
*/
function yikes_mc_toggle_field_label_edit( clicked ) {

	// Are we canceling the edit or are we initializing the edit? Run some conditional logic

	// Let's populate our input field with the current value of the hidden input field (the currently defined label)
	jQuery( clicked ).siblings( '.yikes-mc-edit-field-label-input' ).val(
		jQuery( clicked ).parents( '.expansion-section-title' ).siblings( '.yikes-mc-settings-expansion-section' ).children( '.yikes-mc-merge-field-label' ).val()
	);

	// Default values
	var fadeOut_selectors = jQuery( clicked ).siblings( '.yikes-mc-expansion-section-field-label, .field-type-text' );
	var fadeIn_selectors = jQuery( clicked ).siblings( '.yikes-mc-edit-field-label-input, .yikes-mc-save-field-label-edits-icon' );

	// If clicked element has class 'dashicons-no' we are CANCELING the edit
	if ( jQuery( clicked ).hasClass( 'dashicons-no' ) ) {
		fadeOut_selectors = jQuery( clicked ).siblings( '.yikes-mc-edit-field-label-input, .yikes-mc-save-field-label-edits-icon' );
		fadeIn_selectors = jQuery( clicked ).siblings( '.yikes-mc-expansion-section-field-label, .field-type-text' );

		// Change the dashicon title to something like "Click to edit the label"
		clicked.title = yikes_mailchimp_edit_form.edit_field_label_pencil_title;
	} else {

		// Change the dashicon title to something like "Click to cancel editing. Your changes will not be saved."
		clicked.title = yikes_mailchimp_edit_form.edit_field_label_cancel_title;
	}

	// Switch label from edit icon to X and vise versa
	jQuery( clicked ).toggleClass( 'dashicons-no dashicons-edit' );

	// Toggle fading in/fading out the field-label and field type OR the input field and save icon
	// Use .promise() and .done() to run the callback once for potentially multiple selectors
	fadeOut_selectors.fadeToggle().promise().done( function() {
		fadeIn_selectors.fadeToggle();
	});
}

function yikes_mc_save_field_label_name( clicked_element, list_id, field_data ) {
	var data = {
		action: 'save_field_label_edits',
		list_id: list_id,
		field_data: field_data,
		nonce: yikes_mailchimp_edit_form.save_field_label_nonce
	}

	jQuery.post( yikes_mailchimp_edit_form.ajax_url, data, function( response ) {

		if ( response !== 'undefined' && response.success !== 'undefined' ) {
			var success = response.success;
			if ( success === true ) {

				// Update the field label
				var field_label = field_data['field_name'];
				yikes_mc_update_field_label( clicked_element, field_label );
				yikes_mc_toggle_field_label_edit( jQuery( clicked_element ).siblings( '.dashicons-no' ) );
			} else {

				// Show error message
				var message = '';
				if ( response.data !== 'undefined' && response.data.message !== 'undefined' ) {
					message = response.data.message;
					yikes_mc_display_field_label_error_message( clicked_element, message );
				}
			}
		}
	});
}

/**
* Update field label values
*/
function yikes_mc_update_field_label( element, field_label ) {

	// Update the hidden input field -- this is what tells the backend to update the field
	jQuery( element ).parents( '.expansion-section-title' ).siblings( '.yikes-mc-settings-expansion-section' ).children( '.yikes-mc-merge-field-label' ).val( field_label );

	// Update the actual text on the expansion section 
	// (this is for UI/UX purposes only. On refresh/save, the actual value will be replaced)
	jQuery( element ).siblings( '.yikes-mc-expansion-section-field-label' ).text( field_label );
}

/**
* Display an error message for errors during field label updates
*/
function yikes_mc_display_field_label_error_message( element, message ) {
	jQuery( element ).siblings( '.yikes-mc-edit-field-label-message' ).fadeOut( function() {
		jQuery( this ).text( message ).fadeIn();
	});
}