jQuery(document).ready(function ($) {
    // Highlight all input fields where the validation has failed
    $('#error-box').find('li').each(function () {
        var errorClass = $(this).data('error');
        if (errorClass.length > 0) {
            $('#' + errorClass).parents('div.form-group').addClass('has-error');
        }
    });

    // If available, select the tab where the first error has occurred
    if ($('.tabbable').length > 0) {
        var tabId = $('.tabbable .form-group.has-error:first').parents('.tab-pane').prop('id');
        $('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');
    }
});