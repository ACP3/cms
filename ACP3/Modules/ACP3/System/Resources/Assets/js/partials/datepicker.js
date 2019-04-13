jQuery(document).ready(($) => {
    const $datepickerRange = $('[data-datepicker-range]'),
        $datepicker = $('[data-datepicker]');

    if ($datepickerRange.length > 0) {
        $datepickerRange.each(function () {
            const datepickers = $(this).data('datepicker-range');

            if (datepickers.start && datepickers.end) {
                const $datepickerStart = $(datepickers.start),
                    $datepickerEnd = $(datepickers.end);

                $datepickerStart.datetimepicker();
                $datepickerEnd.datetimepicker();

                $datepickerStart.on('dp.change', function (e) {
                    $datepickerEnd.data('DateTimePicker').minDate(e.date);
                });

                $datepickerEnd.data('DateTimePicker').minDate(datepickers.startDefaultDate);
            }
        });
    }
    if ($datepicker.length > 0) {
        $datepicker.each(function () {
            $(this).datetimepicker();
        });
    }
});
