/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

import { delegateEvent } from "acp3/module-system/Resources/Assets/js/lib/event-handler";

const ratingWrapper = document.getElementById("rating-wrapper");

delegateEvent(ratingWrapper, "click", ".rating__star-input", () => {
  document.querySelector(".rating__average")?.remove();
  document.querySelector(".rating__already-rated")?.remove();

  document.getElementById("js-rating-form-submit").click();
});

// For touch based devices (i.e. w/o support for the hover event), we have to get a little creative
// if there are already ratings and the user taps into an area within the average rating.
delegateEvent(ratingWrapper, "click", ".rating__average .rating__star", (event, elem) => {
  const elemIndex = [...elem.parentElement.children].indexOf(elem);
  const ratingStars = document.querySelectorAll(".rating > .rating__star");

  // .rating__average sets the direction back to ltr, so we have to account for this
  const index = ratingStars.length - 1 - elemIndex;
  ratingStars.item(index).classList.add("rating__star--active");
  ratingStars.item(index).click();
});
