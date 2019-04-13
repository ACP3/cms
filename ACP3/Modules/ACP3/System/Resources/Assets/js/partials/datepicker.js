jQuery(document).ready(($) => {
    const $datepickerRange = $('[data-datepicker-range]'),
        $datepicker = $('[data-datepicker]');

    $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
        icons: {
            time: 'fas fa-clock',
            date: 'fas fa-calendar',
            up: 'fas fa-arrow-up',
            down: 'fas fa-arrow-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'fas fa-calendar-check-o',
            clear: 'fas fa-trash',
            close: 'fas fa-times'
        },
        format: 'YYYY-MM-DD HH:mm'
    });

    if ($datepickerRange.length > 0) {
        $datepickerRange.each(function () {
            const datepickers = $(this).data('datepicker-range');

            const $datepickerStart = $(datepickers[0].element),
                $datepickerEnd = $(datepickers[1].element);

            $datepickerStart.datetimepicker(datepickers[0]);
            $datepickerEnd.datetimepicker(datepickers[1]);

            $datepickerStart.on('change.datetimepicker', function (e) {
                $datepickerEnd.datetimepicker('minDate', e.date);
            });
            $datepickerEnd.on('change.datetimepicker', function (e) {
                $datepickerStart.datetimepicker('maxDate', e.date);
            });
        });
    }
    if ($datepicker.length > 0) {
        $datepicker.each(function () {
            $(this).datetimepicker($(this).data('datepicker'));
        });
    }
});
