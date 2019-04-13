jQuery(document).ready(($) => {
    $('#notify').on('change', function () {
        $('#notify-email')
            .closest('.form-group').toggle(Number(this.value) !== 0);
    }).children('option:selected').trigger('change');
});
