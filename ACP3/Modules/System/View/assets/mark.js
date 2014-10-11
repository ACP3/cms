var cssClassName = 'active';

/**
 * Marks all visible results
 *
 * @param id
 * @param name
 * @param action
 */
function markEntries(id, name, action) {
    var fields = $(id).parents('thead:first').next('tbody').find('input[name="' + name + '[]"]:visible');

    jQuery.each(fields, function () {
        if (action === 'add') {
            $(this).prop('checked', true).parents('tr:first').addClass(cssClassName);
        } else {
            $(this).prop('checked', false).parents('tr:first').removeClass(cssClassName);
        }
    });
}

/**
 *
 * @param checkboxName
 */
jQuery.fn.highlightTableRow = function (checkboxName) {
    var $this = $(this);

    $this.parents('thead')
        .next('tbody').find('tr:has(:checkbox)').click(function (e) {
            var $tableRow = $(this);
            if (e.target.type !== 'checkbox') {
                if (e.target.nodeName === 'A') {
                    return;
                }

                var $elem = $('input[name="' + checkboxName + '[]"]', this);
                $elem.prop('checked', !$elem.is(':checked'));
            }

            $tableRow.toggleClass(cssClassName);

            // Alle Datensätze auf einer Seite wurden markiert
            if ($tableRow.closest('tbody').find('input[name="' + checkboxName + '[]"]:visible').length === $tableRow.closest('tbody').find('tr.' + cssClassName + ':visible').length) {
                $this.prop('checked', true);
            } else {
                $this.prop('checked', false);
            }
        }
    );
};

/**
 *
 * @param options
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
};