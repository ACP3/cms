/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

/* global bootstrap */

import { delegateEvent } from "./event-handler";

const selectedRowCssClassName = "table-active";

/**
 * Marks all visible results
 *
 * @param {HTMLElement} markAllRowsCheckboxElem
 * @param {string} action
 */
export function highlightAllTableRows(markAllRowsCheckboxElem, action) {
  markAllRowsCheckboxElem
    .closest("table")
    .querySelectorAll("tbody tr:not(.d-none):not(.sub-table-header)")
    ?.forEach((elem) => {
      elem.dispatchEvent(new CustomEvent("click", { detail: action, bubbles: true }));
    });
}

/**
 * Highlights a single table row
 *
 * @param {HTMLElement} markAllRowsCheckboxElem
 * @param {HTMLElement} massActionBarElem
 */
export function highlightTableRow(markAllRowsCheckboxElem, massActionBarElem) {
  const tableElem = markAllRowsCheckboxElem.closest("table");
  const checkboxName = markAllRowsCheckboxElem.dataset.checkboxName;

  delegateEvent(tableElem, "click", "tbody tr:not(.sub-table-header)", (event, elem) => {
    const action = event.detail;

    if (event.target.type !== "checkbox") {
      if (
        event.target.nodeName === "A" ||
        event.target.nodeName === "BUTTON" ||
        event.target.closest("a") ||
        event.target.closest("button")
      ) {
        return;
      }

      const rowCheckbox = elem.querySelector(`input[name="${checkboxName}[]"]`);
      rowCheckbox.checked = !rowCheckbox.checked || action === "add";
    }

    if (action === "add") {
      elem.classList.add(selectedRowCssClassName);
    } else {
      elem.classList.toggle(selectedRowCssClassName);
    }

    setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName, massActionBarElem);
  });
}

/**
 *
 * @param {HTMLElement} markAllRowsCheckboxElem
 * @param {string} checkboxName
 * @param {HTMLElement} massActionBarElem
 */
function setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName, massActionBarElem) {
  const tbodyElem = markAllRowsCheckboxElem.closest("table").querySelector("tbody");
  const visibleSelectedTableRows = tbodyElem.querySelectorAll(`tr.${selectedRowCssClassName}:not(.d-none)`).length;
  const visibleMassActionCheckboxes = tbodyElem.querySelectorAll(
    `tr:not(.d-none) input[name="${checkboxName}[]"]`
  ).length;

  markAllRowsCheckboxElem.indeterminate =
    visibleSelectedTableRows > 0 && visibleMassActionCheckboxes !== visibleSelectedTableRows;
  markAllRowsCheckboxElem.checked =
    visibleSelectedTableRows > 0 && visibleMassActionCheckboxes === visibleSelectedTableRows;

  if (massActionBarElem) {
    massActionBarElem.classList.toggle("d-none", visibleSelectedTableRows === 0);
  }
}

/**
 *
 * @param {HTMLElement} massDeleteButtonElem
 * @param options
 */
export function deleteMarkedResults(massDeleteButtonElem, options) {
  const defaults = {
    checkBoxName: "",
    language: {
      confirmationTextSingle: "",
      confirmationTextMultiple: "",
    },
  };
  const settings = { ...defaults, ...options };

  massDeleteButtonElem.addEventListener("click", (event) => {
    event.preventDefault();

    const selectedMassActionCheckboxes = massDeleteButtonElem
      .closest("table")
      .querySelectorAll('input[name="' + settings.checkBoxName + '[]"]:checked');

    const modalElem = massDeleteButtonElem.closest("form").querySelector(".js-mass-delete-modal");
    const modal = bootstrap.Modal.getOrCreateInstance(modalElem);

    modalElem.querySelector(".modal-body").innerHTML =
      selectedMassActionCheckboxes.length === 1
        ? settings.language.confirmationTextSingle
        : settings.language.confirmationTextMultiple.replace("{items}", selectedMassActionCheckboxes.length.toString());

    modal.show();
  });
}
