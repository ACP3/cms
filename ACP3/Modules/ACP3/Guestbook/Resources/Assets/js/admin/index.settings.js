jQuery(document).ready(function ($) {
    $('#notify').on('change', function () {
        $('#notify-email')
            .closest('.form-group').toggle(this.value != 0);
    }).children('option:selected').trigger('change');
});
