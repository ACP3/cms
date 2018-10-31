jQuery(document).ready(function ($) {
    const $resourcesTable = $('#resources-table');
    $resourcesTable.find('tbody tr:not(.bg-light)').hide();

    $resourcesTable.find('.bg-light').click(function () {
        $(this).nextUntil('tr.bg-light').toggle();

        const visibleLength = $resourcesTable.find('tbody tr:has(:checkbox):visible').length,
            allVisibleChecked = visibleLength > 0 && visibleLength === $resourcesTable.find('tbody tr.selected:visible').length;

        $('#mark-all').prop('checked', allVisibleChecked);
    });
});
