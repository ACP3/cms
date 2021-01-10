/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

(($, window) => {
    $('[data-datatable-init]').each(function () {
        const $this = $(this),
            json = $this.data('datatable-init');

        const table = $this.DataTable(json);

        window[$this.attr('id') + 'ReloadDataTable'] = () => {
            table.ajax.reload(null, false);
        };
    });
})(jQuery, window);
