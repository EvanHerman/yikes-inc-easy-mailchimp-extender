( function( $ ) {
	jQuery( document ).ready( function() {

		// Standard Date Fields - loop through each one and initialize datepicker with the input field's date format attr
		jQuery( 'input[data-attr-type="date"]' ).each( function() {
			const element = jQuery( this );

			// Set up a placeholder data-id property on the element. This is used to subvert the datepicker's requirement of unique IDs.
			element.data( 'id', element.attr( 'id' ) )

			// Initialize the datepicker.
			element.datepicker({
				dateFormat: element.data( 'date-format' ).replace( 'yyyy', 'yy' ),
				isRTL: datepicker_settings.rtl,
				dayNames: datepicker_settings.day_names,
				dayNamesMin: datepicker_settings.day_names_min,
				dayNamesShort: datepicker_settings.day_names_short,
				monthNames: datepicker_settings.month_names,
				monthNamesShort: datepicker_settings.month_names_short,
				firstDay: datepicker_settings.first_day,
				changeMonth: datepicker_settings.change_month,
				changeYear: datepicker_settings.change_year,
				minDate: datepicker_settings.min_date,
				maxDate: datepicker_settings.max_date,
				defaultDate: datepicker_settings.default_date,
				numberOfMonths: typeof( datepicker_settings.number_of_months ) === 'string' ? parseInt( datepicker_settings.number_of_months ) : datepicker_settings.number_of_months, // works
				showOtherMonths: datepicker_settings.show_other_months,
				selectOtherMonths: datepicker_settings.select_other_months,
				showAnim: datepicker_settings.show_anim,
				showButtonPanel: datepicker_settings.show_button_panel,
				beforeShowDay: typeof( yikes_mc_before_show_day ) === 'function' ? yikes_mc_before_show_day : null
			});

			// Show the year (relies on some CSS in the yikes-datepicker-styles.css file)
			jQuery( this ).focus( function() {
				handle_conflicting_id_datepicker_hack( $( this ), 'input[data-attr-type="date"]' );
				remove_datepicker_hide_year_class();
			});
		});

		// Birthday Fields - loop through each one and initialize datepicker with the input field's date format attr
		jQuery( 'input[data-attr-type="birthday"]' ).each( function() {
			const element = jQuery( this );

			// Set up a placeholder data-id property on the element. This is used to subvert the datepicker's requirement of unique IDs.
			element.data( 'id', element.attr( 'id' ) )

			element.datepicker({
				dateFormat: element.data( 'date-format' ),
				isRTL: datepicker_settings.rtl,
				dayNames: datepicker_settings.day_names,
				dayNamesMin: datepicker_settings.day_names_min,
				dayNamesShort: datepicker_settings.day_names_short,
				monthNames: datepicker_settings.month_names,
				monthNamesShort: datepicker_settings.month_names_short,
				firstDay: datepicker_settings.first_day,
				changeMonth: datepicker_settings.change_month,
				changeYear: datepicker_settings.change_year,
				minDate: datepicker_settings.min_date,
				maxDate: datepicker_settings.max_date,
				defaultDate: datepicker_settings.default_date,
				numberOfMonths: typeof( datepicker_settings.number_of_months ) === 'string' ? parseInt( datepicker_settings.number_of_months ) : datepicker_settings.number_of_months, // works
				showOtherMonths: datepicker_settings.show_other_months,
				selectOtherMonths: datepicker_settings.select_other_months,
				showAnim: datepicker_settings.show_anim,
				showButtonPanel: datepicker_settings.show_button_panel,
				beforeShowDay: typeof( yikes_mc_before_show_day ) === 'function' ? yikes_mc_before_show_day : null
			});

			// Hide the year (relies on some CSS in the yikes-datepicker-styles.css file)
			jQuery( this ).focus( function() {
				handle_conflicting_id_datepicker_hack( $( this ), 'input[data-attr-type="birthday"]' );
				add_datepicker_hide_year_class();
			});
		});

	});

	function handle_conflicting_id_datepicker_hack( element, elements_selector ) {
		jQuery( elements_selector ).attr( 'id', '' );
		element.attr( 'id', element.data( 'id' ) );
	}

	function remove_datepicker_hide_year_class() {
		jQuery( '#ui-datepicker-div' ).removeClass( 'yikes-datepicker-hide-year' );
	}

	function add_datepicker_hide_year_class() {
		jQuery( '#ui-datepicker-div' ).addClass( 'yikes-datepicker-hide-year' );
	}
})( jQuery );