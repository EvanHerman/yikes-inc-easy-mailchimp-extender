(function ($) {

    function get_datepicker_options(date_format) {
        return {
            dateFormat: date_format,
            isRTL: datepicker_settings.rtl,
            dayNames: datepicker_settings.day_names,
            dayNamesMin: datepicker_settings.day_names_min,
            dayNamesShort: datepicker_settings.day_names_short,
            monthNames: datepicker_settings.month_names,
            monthNamesShort: datepicker_settings.month_names_short,
            firstDay: datepicker_settings.first_day,
            changeMonth: datepicker_settings.change_month,
            changeYear: datepicker_settings.change_year,
            yearRange: datepicker_settings.year_range,
            minDate: datepicker_settings.min_date,
            maxDate: datepicker_settings.max_date,
            defaultDate: datepicker_settings.default_date,
            numberOfMonths: typeof (datepicker_settings.number_of_months) === 'string' ? parseInt(datepicker_settings.number_of_months) : datepicker_settings.number_of_months, // works
            showOtherMonths: datepicker_settings.show_other_months,
            selectOtherMonths: datepicker_settings.select_other_months,
            showAnim: datepicker_settings.show_anim,
            showButtonPanel: datepicker_settings.show_button_panel,
            beforeShowDay: typeof (yikes_mc_before_show_day) === 'function' ? yikes_mc_before_show_day : null
        }
    }

    // Initialize the datepicker.
    $(document).ready(function () {

        // Date Fields.
        $('body').on('focus', 'input[data-attr-type="date"]', function () {
            const element = $(this);

            // Handle conflicting IDs.
            handle_conflicting_id_datepicker_hack(element, 'input[data-attr-type="date"]');

            // Initialize the datepicker.
            element.datepicker(get_datepicker_options(element.data('date-format').replace('yyyy', 'yy')));

            // Show the year.
            remove_datepicker_hide_year_class();
        });


        // Birthday Fields.
        $('body').on('focus', 'input[data-attr-type="birthday"]', function () {
            const element = $(this);

            // Handle conflicting IDs.
            handle_conflicting_id_datepicker_hack(element, 'input[data-attr-type="birthday"]');

            // Initialize the datepicker.
            element.datepicker(get_datepicker_options(element.data('date-format')));

            // Hide the year (relies on some CSS in the yikes-datepicker-styles.css file).
            add_datepicker_hide_year_class();
        });

    });

    function handle_conflicting_id_datepicker_hack(element, elements_selector) {
        $(elements_selector).each(function () {
            if (this.id.length) {
                $(this).data('id', this.id);
            }
        });
        $(elements_selector).attr('id', '');
        element.attr('id', element.data('id'));
    }

    function remove_datepicker_hide_year_class() {
        $('#ui-datepicker-div').removeClass('yikes-datepicker-hide-year');
    }

    function add_datepicker_hide_year_class() {
        $('#ui-datepicker-div').addClass('yikes-datepicker-hide-year');
    }
})(jQuery);
