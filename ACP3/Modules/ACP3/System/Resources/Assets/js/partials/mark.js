import {highlightAllTableRows, highlightTableRow, deleteMarkedResults} from '../lib/mark';

(($) => {
    const $markAll = $('[data-mark-all-id]');

    $markAll.each((index, element) => {
        const $this = $(element);

        $this
            .click(() => {
                highlightAllTableRows(element, $this.is(':checked') ? 'add' : 'remove');
            });

        highlightTableRow(element);
    });

    deleteMarkedResults(
        $('form #adm-list .fa-trash').closest('.btn'),
        $markAll.data('delete-options')
    );
})(jQuery);
