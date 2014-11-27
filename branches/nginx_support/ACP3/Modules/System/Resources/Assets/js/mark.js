var cssClassName = 'info';

/**
 * Marks all visible results
 *
 * @param $markAllElem
 * @param name
 * @param action
 */
function markEntries($markAllElem, name, action) {
    var fields = $markAllElem.parents('thead:first').next('tbody').find('input[name="' + name + '[]"]:visible');

    jQuery.each(fields, function () {
        var $tableRows = $(this).prop('checked', (action === 'add')).parents('tr:first');

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
    var $this = $(this);

    $this.parents('thead')
        .next('tbody').find('tr:has(:checkbox)').click(function (e) {
            var $tableRow = $(this),
                $tbody = $tableRow.closest('tbody');

            if (e.target.type !== 'checkbox') {
                if (e.target.nodeName === 'A') {
                    return;
                }

                var $elem = $('input[name="' + checkboxName + '[]"]', this);
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
    var defaults = {
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

        var $entries = $('form .table input[name="' + settings.checkBoxName + '[]"]:checked');

        if ($entries.length > 0) {
            var data = {
                action: 'confirmed',
                entries: []
            };

            $entries.each(function () {
                data.entries.push($(this).val());
            });

            var confirmationText = $entries.length == 1 ? settings.language.confirmationTextSingle : (settings.language.confirmationTextMultiple.replace('{items}', $entries.length));

            bootbox.confirm(confirmationText, function (result) {
                if (result) {
                    var $form = $this.closest('form');

                    $form.formSubmit(data);
                    $form.triggerHandler('submit');
                }
            });
        } else {
            bootbox.alert(settings.language.noEntriesSelectedText);
        }
    });

    return $this;
};

jQuery(document).ready(function($) {
    var $markAll = $('[data-mark-all-id]');

    $markAll.each(function () {
        var $this = $(this);

        $this
            .click(function () {
                markEntries($this, $this.data('checkbox-name'), $this.is(':checked') ? 'add' : 'remove');
            })
            .highlightTableRow($this.data('checkbox-name'));
    });

    $('form #adm-list input[type="image"]').deleteMarkedResults($markAll.data('delete-options'));
});