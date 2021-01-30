/* global flatpickr */

const dateRangePickers = document.querySelectorAll("[data-datepicker-range]");

dateRangePickers.forEach((dateRangePicker) => {
  const dateRangePickerConfig = JSON.parse(dateRangePicker.dataset.datepickerRange);

  const datePickerEnd = flatpickr(dateRangePickerConfig.end, {
    altInput: true,
    altFormat: dateRangePickerConfig.altFormat,
    enableTime: dateRangePickerConfig.enableTime,
    minDate: dateRangePickerConfig.startDefaultDate,
    minuteIncrement: 1,
    time_24hr: true,
    weekNumbers: true,
    wrap: true,
  });

  flatpickr(dateRangePickerConfig.start, {
    altInput: true,
    altFormat: dateRangePickerConfig.altFormat,
    enableTime: dateRangePickerConfig.enableTime,
    minuteIncrement: 1,
    onChange: (selectedDates) => {
      if (selectedDates[0] > datePickerEnd.selectedDates[0]) {
        datePickerEnd.setDate(selectedDates[0]);
      }

      datePickerEnd.set("minDate", selectedDates[0]);
    },
    time_24hr: true,
    weekNumbers: true,
    wrap: true,
  });
});
