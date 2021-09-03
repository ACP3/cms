import { highlightAllTableRows, highlightTableRow, deleteMarkedResults } from "../lib/mass-action";

document.querySelectorAll("[data-mark-all-id]").forEach((markAllElem) => {
  // Get the unique identifier of the grid, so that we can find the element responsible for the mass actions
  const tableIdentifier = markAllElem.closest("table").id;
  const massActionBar = document.getElementById(`${tableIdentifier}-mass-delete-bar`);

  markAllElem.addEventListener("click", () =>
    highlightAllTableRows(markAllElem, markAllElem.checked ? "add" : "remove")
  );

  highlightTableRow(markAllElem, massActionBar);

  deleteMarkedResults(
    document.getElementById(`${tableIdentifier}-mass-delete`),
    JSON.parse(markAllElem.dataset.deleteOptions)
  );
});
