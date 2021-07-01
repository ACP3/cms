/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

const cssClassName = "info";

/**
 * Marks all visible results
 *
 * @param {Element} markAllRowsCheckboxElem
 * @param {string} action
 */
export function highlightAllTableRows(markAllRowsCheckboxElem, action) {
  jQuery(markAllRowsCheckboxElem).closest("table").find("tr:has(td :checkbox:visible)").trigger("click", [action]);
}

/**
 * Highlights a single table row
 *
 * @param {Element} markAllRowsCheckboxElem
 * @param {Element} massActionBarElem
 */
export function highlightTableRow(markAllRowsCheckboxElem, massActionBarElem) {
  const $markAllCheckbox = jQuery(markAllRowsCheckboxElem),
    $table = $markAllCheckbox.closest("table"),
    checkboxName = $markAllCheckbox.data("checkbox-name");

  $table.on("click", "tr:has(td :checkbox)", function (event, action) {
    if (event.target.type !== "checkbox") {
      if (event.target.nodeName === "A" || event.target.closest("a")) {
        return;
      }

      const rowCheckbox = this.querySelector(`input[name="${checkboxName}[]"]`);
      rowCheckbox.checked = !rowCheckbox.checked || action === "add";
    }

    if (action === "add") {
      this.classList.add(cssClassName);
    } else {
      this.classList.toggle(cssClassName);
    }

    setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName, massActionBarElem);
  });

  jQuery(document).on("draw.dt", () => {
    setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName, massActionBarElem);
  });
}

/**
 *
 * @param {Element} markAllRowsCheckboxElem
 * @param {string} checkboxName
 * @param {Element} massActionBarElem
 */
function setMarkAllCheckboxState(markAllRowsCheckboxElem, checkboxName, massActionBarElem) {
  const $tbody = jQuery(markAllRowsCheckboxElem).closest("table").find("tbody");
  const visibleSelectedTableRows = $tbody.find("tr." + cssClassName + ":visible").length;
  const visibleMassActionCheckboxes = $tbody.find('input[name="' + checkboxName + '[]"]:visible').length;

  markAllRowsCheckboxElem.indeterminate =
    visibleSelectedTableRows > 0 && visibleMassActionCheckboxes !== visibleSelectedTableRows;
  markAllRowsCheckboxElem.checked =
    visibleSelectedTableRows > 0 && visibleMassActionCheckboxes === visibleSelectedTableRows;

  if (massActionBarElem) {
    massActionBarElem.classList.toggle("hidden", visibleSelectedTableRows === 0);
  }
}

/**
 *
 * @param {HTMLElement} elem
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
