const cssClassName = 'row-selected bg-light';

/**
 * Marks all visible results
 *
 * @param $massActionCheckbox
 * @param name
 * @param action
 */
const markEntries = ($massActionCheckbox, name, action) => {
    const $visibleCheckboxes = $massActionCheckbox
        .parents('thead:first')
        .next('tbody')
        .find('input[name="' + name + '[]"]:visible');

    jQuery.each($visibleCheckboxes, function () {
        const $tableRows = jQuery(this)
            .prop('checked', (action === 'add'))
            .parents('tr:first');

        $tableRows.toggleClass(cssClassName, action === 'add');
    });
};

const toggleMassActionsBar = () => {
    const $massActionsBar = jQuery('.datagrid-mass-actions');

    if ($massActionsBar.find('.container').children().length === 0) {
        return;
    }

    const $markedEntries = jQuery('.datagrid-column__mass-action:not(th) :checkbox:checked');

    $massActionsBar.toggleClass('d-none', $markedEntries.length === 0);
};

const setMassActionCheckboxState = ($massActionCheckbox, $dataGridBody, checkboxName) => {
    const visibleCheckboxes = $dataGridBody.find('input[name="' + checkboxName + '[]"]:visible').length,
        checkedCheckboxes = $dataGridBody.find('input[name="' + checkboxName + '[]"]:checked').length;

    if (visibleCheckboxes === 0 || checkedCheckboxes === 0) {
        $massActionCheckbox.prop('checked', false);
        $massActionCheckbox.prop('indeterminate', false);
    } else if (checkedCheckboxes < visibleCheckboxes) {
        $massActionCheckbox.prop('checked', false);
        $massActionCheckbox.prop('indeterminate', true);
    } else {
        $massActionCheckbox.prop('checked', true);
        $massActionCheckbox.prop('indeterminate', false);
    }
};

/**
 *
 * @param checkboxName
 * @returns {*|jQuery|HTMLElement}
 */
jQuery.fn.highlightTableRow = function (checkboxName) {
    const $massActionCheckbox = $(this);

    $massActionCheckbox
        .closest('table')
        .on(
            'click', 'tr:has(td :checkbox)',
            function (e) {
                const $tableRow = $(this),
                    $tbody = $tableRow.closest('tbody');

                if (e.target.type !== 'checkbox') {
                    if (e.target.nodeName === 'A') {
                        return;
                    }

                    const $elem = $('input[name="' + checkboxName + '[]"]', this);
                    $elem.prop('checked', !$elem.is(':checked'));
                }

                $tableRow.toggleClass(cssClassName);

                toggleMassActionsBar();
                setMassActionCheckboxState($massActionCheckbox, $tbody, checkboxName);
            }
        );

    return $massActionCheckbox;
};

/**
 *
 * @param options
 * @returns {*|jQuery|HTMLElement}
 */
jQuery.fn.deleteMarkedResults = function (options) {
    const defaults = {
            checkBoxName: '',
            language: {
                confirmationTextSingle: '',
                confirmationTextMultiple: '',
                noEntriesSelectedText: ''
            },
            bootboxLocale: 'en'
        },
        $this = $(this),
        settings = $.extend(defaults, options);

    $this.on('click', function (e) {
        e.preventDefault();

        const $entries = $('form .table input[name="' + settings.checkBoxName + '[]"]:checked');

        if ($entries.length > 0) {
            const confirmationText = $entries.length === 1
                ? settings.language.confirmationTextSingle
                : (settings.language.confirmationTextMultiple.replace('{items}', $entries.length));

            bootbox.confirm({
                message: confirmationText,
                locale: settings.bootboxLocale,
                callback: (result) => {
                    if (result) {
                        const $form = $this.closest('form');

                        $form.formSubmit({
                            customFormData: {
                                action: 'confirmed'
                            }
                        });
                        $form.triggerHandler('submit');
                    }
                }
            });
        } else {
            bootbox.alert({
                message: settings.language.noEntriesSelectedText,
                locale: settings.bootboxLocale
            });
        }
    });

    return $this;
};

jQuery(document).ready(($) => {
    const $massActionCheckboxes = $('[data-mark-all-id]');

    $massActionCheckboxes.each((index, element) => {
        const $this = $(element);

        $this
            .click(function () {
                markEntries($this, $this.data('checkbox-name'), $this.is(':checked') ? 'add' : 'remove');
                toggleMassActionsBar();
            })
            .highlightTableRow($this.data('checkbox-name'));
    });

    $('.datagrid-mass-actions .fa-trash')
        .closest('.btn')
        .deleteMarkedResults($massActionCheckboxes.data('delete-options'));
});
