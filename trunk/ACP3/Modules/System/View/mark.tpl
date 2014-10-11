{include_js module="system" file="forms"}
{include_js module="system" file="mark" depends="bootbox"}
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#{$mark_all_id}').click(function () {
            markEntries('#{$mark_all_id}', '{$checkbox_name}', $(this).is(':checked') ? 'add' : 'remove');
        }).highlightTableRow('{$checkbox_name}');

        {if $is_initialized === false}
            var options = {
                checkBoxName: '{$checkbox_name}',
                language: {
                    confirmationTextSingle: '{lang t="system|confirm_delete_single"}',
                    confirmationTextMultiple: '{lang t="system|confirm_delete_multiple"}',
                    noEntriesSelectedText: '{lang t="system|no_entries_selected"}'
                }
            };

            $('form #adm-list input[type="image"]').deleteMarkedResults(options);
        {/if}
    });
</script>