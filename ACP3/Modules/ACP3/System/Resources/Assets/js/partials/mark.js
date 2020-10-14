const cssClassName = 'info';

/**
 * Marks all visible results
 *
 * @param $markAllElem
 * @param name
 * @param action
 */
function markEntries($markAllElem, name, action) {
    const fields = $markAllElem.parents('thead:first').next('tbody').find('input[name="' + name + '[]"]:visible');

    jQuery.each(fields, function () {
        const $tableRows = $(this).prop('checked', (action === 'add')).parents('tr:first');

        if (action === 'add') {
            $tableRows.addClass(cssClassName);
        } else {
            $tableRows.removeClass(cssClassName);
        }
    });
}

/**
 *
 * @param checkboxName
 * @returns {*|jQuery|HTMLElement}
 */
jQuery.fn.highlightTableRow = function (checkboxName) {
    const $markAllCheckbox = $(this);

    $markAllCheckbox
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

                // Alle Datensätze auf einer Seite wurden markiert
                $markAllCheckbox.prop(
                    'checked',
                    ($tbody.find('input[name="' + checkboxName + '[]"]:visible').length === $tbody.find('tr.' + cssClassName + ':visible').length)
                );
            }
        );

    return $markAllCheckbox;
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
    const $markAll = $('[data-mark-all-id]');

    $markAll.each((index, element) => {
        const $this = $(element);

        $this
            .click(function () {
                markEntries($this, $this.data('checkbox-name'), $this.is(':checked') ? 'add' : 'remove');
            })
            .highlightTableRow($this.data('checkbox-name'));
    });

    $('form #adm-list .fa-trash')
        .closest('.btn')
        .deleteMarkedResults($markAll.data('delete-options'));
});
