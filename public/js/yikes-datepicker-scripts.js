jQuery( document ).ready( function() {

	// Standard Date Fields - loop through each one and initialize datepicker with the input field's date format attr
	jQuery( 'input[data-attr-type="date"]' ).each( function() {
		jQuery( this ).datepicker({
			dateFormat: jQuery( this ).data( 'date-format' ).replace( 'yyyy', 'yy' ),
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
		jQuery( this ).click( function() {
			jQuery( '#ui-datepicker-div' ).removeClass( 'yikes-datepicker-hide-year' );
		});
	});

	// Birthday Fields - loop through each one and initialize datepicker with the input field's date format attr
	jQuery( 'input[data-attr-type="birthday"]' ).each( function() {
		jQuery( this ).datepicker({
			dateFormat: jQuery( this ).data( 'date-format' ),
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
		jQuery( this ).click( function() {
			jQuery( '#ui-datepicker-div' ).addClass( 'yikes-datepicker-hide-year' );
		});
	});

});