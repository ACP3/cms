/* global flatpickr */

const datePickers = document.querySelectorAll("[data-datepicker]");

datePickers.forEach((datePicker) => {
  const datepickerConfig = JSON.parse(datePicker.dataset.datepicker);

  flatpickr(datepickerConfig.element, {
    allowInput: true,
    altInput: true,
    altFormat: datepickerConfig.altFormat,
    enableTime: datepickerConfig.enableTime,
    minuteIncrement: 1,
    time_24hr: true,
    weekNumbers: true,
    wrap: true,
  });
});
