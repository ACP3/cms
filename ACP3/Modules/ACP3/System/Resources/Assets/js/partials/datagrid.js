/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */
import "datatables.net-bs5/css/dataTables.bootstrap5.css";
import DataTable from "datatables.net-bs5/js/dataTables.bootstrap5.mjs";

((window, document) => {
  document.querySelectorAll("[data-datatable-init]").forEach(function (elem) {
    const json = JSON.parse(elem.dataset.datatableInit);

    const table = new DataTable(elem, json);

    window[elem.id + "ReloadDataTable"] = () => {
      table.ajax.reload(null, false);
    };
  });
})(window, document);
