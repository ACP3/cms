import { highlightAllTableRows, highlightTableRow, deleteMarkedResults } from "../lib/mass-action";

document.querySelectorAll("[data-mark-all-id]").forEach((element) => {
  // Get the unique identifier of the grid, so that we can find the element responsible for the mass actions
  const tableIdentifier = element.closest("table").id;
  const massActionBar = document.getElementById(`${tableIdentifier}-mass-delete-bar`);

  element.addEventListener("click", () =>
    highlightAllTableRows(element, element.checked ? "add" : "remove", massActionBar)
  );

  highlightTableRow(element, massActionBar);

  deleteMarkedResults(
    document.getElementById(`${tableIdentifier}-mass-delete`),
    JSON.parse(element.dataset.deleteOptions)
  );
});
