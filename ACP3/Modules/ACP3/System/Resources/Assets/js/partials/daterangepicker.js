import { delegateEvent } from "../lib/event-handler";

const dateRangePickers = document.querySelectorAll("[data-datepicker-range]");

dateRangePickers.forEach((dateRangePicker) => {
  const dateRangePickerConfig = JSON.parse(dateRangePicker.dataset.datepickerRange);

  delegateEvent(document, "change", dateRangePickerConfig.start, (e, elem) => {
    const endDatepicker = document.querySelector(dateRangePickerConfig.end);

    if (elem.value > endDatepicker.value) {
      endDatepicker.min = elem.value;
      endDatepicker.value = elem.value;
    }
  });
});
