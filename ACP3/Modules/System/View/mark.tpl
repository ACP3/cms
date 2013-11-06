{js_libraries enable="bootbox"}
<script type="text/javascript">
    function mark_entries(id, name, action) {
        var fields = $(id).parents('thead:first').next('tbody').find('input[name="' + name + '[]"]:visible');

        jQuery.each(fields, function () {
            if (action === 'add') {
                $(this).prop('checked', true).parents('tr:first').addClass('info');
            } else {
                $(this).prop('checked', false).parents('tr:first').removeClass('info');
            }
        });
    }

    $(document).ready(function () {
        $('#{$mark_all_id}').click(function () {
            mark_entries('#{$mark_all_id}', '{$checkbox_name}', $(this).is(':checked') ? 'add' : 'remove');
            // Checkbox durch Klick auf Tabellenzeile markieren
        }).parents('thead').next('tbody').find('tr:has(:checkbox)').click(function (e) {
                    if (e.target.type !== 'checkbox') {
                        if (e.target.nodeName === 'A')
                            return;

                        var $elem = $('input[name="{$checkbox_name}[]"]', this);
                        $elem.prop('checked', $elem.is(':checked') ? false : true);
                    }

                    $(this).toggleClass('info');

                    // Alle Datensätze auf einer Seite wurden markiert
                    if ($(this).parents('tbody').find('input[name="{$checkbox_name}[]"]:visible').length === $(this).parents('tbody').find('tr.info:visible').length) {
                        $('#{$mark_all_id}').prop('checked', true);
                    } else {
                        $('#{$mark_all_id}').prop('checked', false);
                    }
                });

        $('form #adm-list input[type=image]').click(function () {
            var entries = $('form .table input[name="{$checkbox_name}[]"]:checked') || [];
            var ary = '';

            jQuery.each(entries, function () {
                ary = ary + $(this).val() + '|';
            });

            if (ary !== '') {
                bootbox.confirm('{lang t="system|confirm_delete"}', function (result) {
                    if (result) {
                        location.href = $('.table').parents('form').prop('action') + 'entries_' + ary.substr(0, ary.length - 1) + '/action_confirmed/';
                    }
                });
            } else {
                bootbox.alert('{lang t="system|no_entries_selected"}');
            }
            return false;
        });
    });
</script>