jQuery(document).ready(function ($) {
    $('input[name="readmore"]').on('click change', function () {
        $('#readmore-container').toggle(this.value == 1);
    }).filter(':checked').click();
});
