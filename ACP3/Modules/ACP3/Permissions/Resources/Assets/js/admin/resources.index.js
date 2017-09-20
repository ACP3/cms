jQuery(document).ready(function ($) {
    const $resourcesTable = $('#resources-table');
    $resourcesTable.find('tbody tr:not(.sub-table-header)').hide();

    $resourcesTable.find('.sub-table-header').click(function () {
        $(this).nextUntil('tr.sub-table-header').toggle();

        const visibleLength = $resourcesTable.find('tbody tr:has(:checkbox):visible').length,
            allVisibleChecked = visibleLength > 0 && visibleLength === $resourcesTable.find('tbody tr.selected:visible').length;

        $('#mark-all').prop('checked', allVisibleChecked);
    });
});
