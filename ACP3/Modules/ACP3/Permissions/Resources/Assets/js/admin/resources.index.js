((document) => {
  const markAllCheckbox = document.getElementById("mark-all");

  const resourcesTable = document.getElementById("resources-table");
  resourcesTable.querySelectorAll("tbody tr:not(.sub-table-header)").forEach((elem) => {
    elem.classList.add("d-none");
  });

  resourcesTable.querySelectorAll(".sub-table-header").forEach((elem) => {
    elem.addEventListener("click", () => {
      resourcesTable
        .querySelectorAll('[data-module-name="' + elem.dataset.moduleName + '"]:not(.sub-table-header)')
        .forEach((resource) => {
          resource.classList.toggle("d-none");
        });

      const visibleLength = resourcesTable.querySelectorAll("tbody tr:not(.d-none)").length;
      const selectedElementsLength = resourcesTable.querySelectorAll("tbody tr.table-info").length;
      const allVisibleChecked = visibleLength > 0 && visibleLength === selectedElementsLength;

      markAllCheckbox.checked = allVisibleChecked;
      markAllCheckbox.indeterminate = selectedElementsLength > 0 && !allVisibleChecked;
    });
  });
})(document);
