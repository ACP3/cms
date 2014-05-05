{js_libraries enable="bootbox"}
{include_js module="system" file="forms"}
{include_js module="system" file="mark"}
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#{$mark_all_id}').click(function () {
            markEntries('#{$mark_all_id}', '{$checkbox_name}', $(this).is(':checked') ? 'add' : 'remove');
        }).highlightTableRow('{$checkbox_name}');

        {if $is_initialized === false}
        $('form #adm-list input[type="image"]').deleteMarkedResults('{$checkbox_name}', '{lang t="system|confirm_delete"}', '{lang t="system|no_entries_selected"}');
        {/if}
    });
</script>