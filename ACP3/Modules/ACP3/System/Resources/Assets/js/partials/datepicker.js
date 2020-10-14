jQuery(document).ready(($) => {
    const $datepickerRange = $('[data-datepicker-range]'),
        $datepicker = $('[data-datepicker]'),
    datepickerIcons = {
        time: 'fas fa-clock',
        date: 'fas fa-calendar',
        up: 'fas fa-chevron-up',
        down: 'fas fa-chevron-down',
        previous: 'fas fa-chevron-left',
        next: 'fas fa-chevron-right',
        today: 'fas fa-calendar-day',
        clear: 'fas fa-trash',
        close: 'fas fa-remove',
    };

    if ($datepickerRange.length > 0) {
        $datepickerRange.each(function () {
            const datepickers = $(this).data('datepicker-range');

            if (datepickers.start && datepickers.end) {
                const $datepickerStart = $(datepickers.start),
                    $datepickerEnd = $(datepickers.end);

                $datepickerStart.datetimepicker({ icons: datepickerIcons });
                $datepickerEnd.datetimepicker({ icons: datepickerIcons });

                $datepickerStart.on('dp.change', function (e) {
                    $datepickerEnd.data('DateTimePicker').minDate(e.date);
                });

                $datepickerEnd.data('DateTimePicker').minDate(datepickers.startDefaultDate);
            }
        });
    }
    if ($datepicker.length > 0) {
        $datepicker.each(function () {
            $(this).datetimepicker({ icons: datepickerIcons });
        });
    }
});
