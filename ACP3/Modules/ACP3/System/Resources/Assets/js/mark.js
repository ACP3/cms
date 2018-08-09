let cssClassName = 'info';

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
        let $tableRows = $(this).prop('checked', (action === 'add')).parents('tr:first');

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
    const $this = $(this);

    $this.parents('thead')
        .next('tbody')
        .find('tr:has(:checkbox)')
        .click(
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
                $this.prop('checked', ($tbody.find('input[name="' + checkboxName + '[]"]:visible').length === $tbody.find('tr.' + cssClassName + ':visible').length));
            }
        );

    return $this;
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
            }
        },
        $this = $(this),
        settings = $.extend(defaults, options);

    $this.on('click', function (e) {
        e.preventDefault();

        const $entries = $('form .table input[name="' + settings.checkBoxName + '[]"]:checked');

        if ($entries.length > 0) {
            const data = {action: 'confirmed'},
                confirmationText = $entries.length === 1
                    ? settings.language.confirmationTextSingle
                    : (settings.language.confirmationTextMultiple.replace('{items}', $entries.length));

            bootbox.confirm(confirmationText, function (result) {
                if (result) {
                    let $form = $this.closest('form');

                    $form.formSubmit({customFormData: data});
                    $form.triggerHandler('submit');
                }
            });
        } else {
            bootbox.alert(settings.language.noEntriesSelectedText);
        }
    });

    return $this;
};

jQuery(document).ready(function ($) {
    const $markAll = $('[data-mark-all-id]');

    $markAll.each(function () {
        const $this = $(this);

        $this
            .click(function () {
                markEntries($this, $this.data('checkbox-name'), $this.is(':checked') ? 'add' : 'remove');
            })
            .highlightTableRow($this.data('checkbox-name'));
    });

    $('form #adm-list .glyphicon-remove')
        .closest('.btn')
        .deleteMarkedResults($markAll.data('delete-options'));
});
