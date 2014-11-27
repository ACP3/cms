jQuery(document).ready(function ($) {
    // At first, remove all previous validation error states
    $('form .form-group.has-error').removeClass('has-error');

    // Next, highlight all input fields where the validation has failed
    $('#error-box').find('li').each(function () {
        var errorClass = $(this).data('error');
        if (errorClass.length > 0) {
            var $elem = $('[id|="' + errorClass + '"]');
            if ($elem.length > 0) {
                $elem.closest('div.form-group').addClass('has-error');
            }
        }
    });

    // As the last step, select the tab where the first error has occurred
    if ($('.tabbable').length > 0) {
        var tabId = $('.tabbable .form-group.has-error:first').closest('.tab-pane').prop('id');
        $('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');
    }
});