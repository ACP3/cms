/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const cssClassName = "info";

/**
 * Marks all visible results
 *
 * @param {HTMLElement} markAllRowsCheckboxElem
 * @param {string} action
 */
export function highlightAllTableRows(markAllRowsCheckboxElem, action) {
  jQuery(markAllRowsCheckboxElem).closest("table").find("tr:has(td :checkbox)").trigger("click", [action]);
}

/**
 *
 * @param {HTMLElement} markAllRowsCheckboxElem
 */
export function highlightTableRow(markAllRowsCheckboxElem) {
  const $markAllCheckbox = jQuery(markAllRowsCheckboxElem),
    $table = $markAllCheckbox.closest("table"),
    checkboxName = $markAllCheckbox.data("checkbox-name");

  $table.on("click", "tr:has(td :checkbox)", function (e, action) {
    if (e.target.type !== "checkbox") {
      if (e.target.nodeName === "A" || jQuery(e.target).closest("a").length) {
        return;
      }

      const $rowCheckbox = jQuery('input[name="' + checkboxName + '[]"]', this);
      $rowCheckbox.prop("checked", !$rowCheckbox.is(":checked") || action === "add");
    }

    if (action === "add") {
      this.classList.add(cssClassName);
    } else {
      this.classList.toggle(cssClassName);
    }

    setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName);
  });

  jQuery(document).on("draw.dt", () => {
    setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName);
  });
}

/**
 *
 * @param {HTMLElement} markAllRowsCheckboxElem
 * @param {string} checkboxName
 */
function setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName) {
  const $tbody = jQuery(markAllRowsCheckboxElem).closest("table").find("tbody");
  const visibleSelectedTableRows = $tbody.find("tr." + cssClassName + ":visible").length;
  const visibleMassActionCheckboxes = $tbody.find('input[name="' + checkboxName + '[]"]:visible').length;

  markAllRowsCheckboxElem.indeterminate =
    visibleSelectedTableRows > 0 && visibleMassActionCheckboxes !== visibleSelectedTableRows;
  markAllRowsCheckboxElem.checked =
    visibleSelectedTableRows > 0 && visibleMassActionCheckboxes === visibleSelectedTableRows;
}

/**
 *
 * @param {HTMLElementEventMap} elem
 * @param options
 */
export function deleteMarkedResults(elem, options) {
  const defaults = {
      checkBoxName: "",
      language: {
        confirmationTextSingle: "",
        confirmationTextMultiple: "",
        noEntriesSelectedText: "",
      },
      bootboxLocale: "en",
    },
    $this = jQuery(elem),
    settings = { ...defaults, ...options };

  $this.on("click", function (e) {
    e.preventDefault();

    const $selectedMassActionCheckboxes = jQuery('form .table input[name="' + settings.checkBoxName + '[]"]:checked');

    if ($selectedMassActionCheckboxes.length > 0) {
      const confirmationText =
        $selectedMassActionCheckboxes.length === 1
          ? settings.language.confirmationTextSingle
          : settings.language.confirmationTextMultiple.replace("{items}", $selectedMassActionCheckboxes.length);

      bootbox.confirm({
        message: confirmationText,
        locale: settings.bootboxLocale,
        callback: (result) => {
          if (result) {
            const $form = $this.closest("form");

            $form.formSubmit({
              customFormData: {
                action: "confirmed",
              },
            });
            $form.triggerHandler("submit");
          }
        },
      });
    } else {
      bootbox.alert({
        message: settings.language.noEntriesSelectedText,
        locale: settings.bootboxLocale,
      });
    }
  });
}
