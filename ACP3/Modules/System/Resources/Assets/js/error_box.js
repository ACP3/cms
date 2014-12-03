jQuery(document).ready(function ($) {
    var $errorBox = $('#error-box');

    // At first, remove all previous validation error states
    $('form .form-group.has-error')
        .removeClass('has-error')
        .find('.validation-failed').remove();

    // Next, highlight all input fields where the validation has failed
    $errorBox.find('li').each(function () {
        var $this = $(this),
            errorClass = $this.data('error');
        if (errorClass.length > 0) {
            var $elem = $('[id|="' + errorClass + '"]').filter(':not([id$="container"])');
            if ($elem.length > 0) {
                // Add CSS class that the validation for this entry has failed
                $elem.closest('div.form-group').addClass('has-error');

                // Move the error message to the responsible input field(s)
                // and remove the list items for the error box container
                $elem.closest('div').append('<small class="help-block validation-failed"><i class="glyphicon glyphicon-remove"></i> ' + $this.html() + '</small>');
                $this.remove();
            }
        }
    });

    // if all list items have been removes, remove the error box container too
    if ($errorBox.find('li').length == 0) {
        $errorBox.remove();
    }

    // As the last step, select the tab where the first error has occurred
    if ($('.tabbable').length > 0) {
        var tabId = $('.tabbable .form-group.has-error:first').closest('.tab-pane').prop('id');
        $('.tabbable .nav-tabs a[href="#' + tabId + '"]').tab('show');
    }
});