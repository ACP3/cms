/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */
import DataTable from "datatables.net-bs5";

(($, window) => {
  $("[data-datatable-init]").each(function () {
    const $this = $(this),
      json = $this.data("datatable-init");

    const table = new DataTable($this, json);

    window[this.id + "ReloadDataTable"] = () => {
      table.ajax.reload(null, false);
    };
  });
})(jQuery, window);
