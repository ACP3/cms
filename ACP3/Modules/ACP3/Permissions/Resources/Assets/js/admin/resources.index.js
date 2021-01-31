(($) => {
  const $resourcesTable = $("#resources-table");
  $resourcesTable.find("tbody tr:not(.sub-table-header)").hide();

  $resourcesTable.find(".sub-table-header").click(function () {
    $(this).nextUntil("tr.sub-table-header").toggle();

    const visibleLength = $resourcesTable.find("tbody tr:has(:checkbox):visible").length;
    const selectedElementsLength = $resourcesTable.find("tbody tr.selected:visible").length;
    const allVisibleChecked = visibleLength > 0 && visibleLength === selectedElementsLength;

    const markAllCheckbox = document.getElementById("mark-all");

    markAllCheckbox.checked = allVisibleChecked;
    markAllCheckbox.indeterminate = selectedElementsLength > 0 && !allVisibleChecked;
  });
})(jQuery);
