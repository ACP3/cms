jQuery(document).ready(function ($) {
    $('#page-break-form').find('.modal-footer button.btn-primary').click(function (e) {
        e.preventDefault();

        var $tocTitle = $('#toc-title'),
            text;

        if ($tocTitle.val().length > 0) {
            text = '<hr class="page-break" title="' + $tocTitle.val() + '" />';
        } else {
            text = '<hr class="page-break" />';
        }

        wysiwygCallback(text);
        $('#page-break-form').modal('hide');
    });
});