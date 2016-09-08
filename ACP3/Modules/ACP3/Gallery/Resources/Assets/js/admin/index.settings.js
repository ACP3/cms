jQuery(document).ready(function ($) {
    $('input[name="overlay"]')
        .on('change click', function () {
            $('#comments-container').toggle(this.value == 0);
        })
        .filter(':checked').trigger('click');
});
