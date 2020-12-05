(($) => {
    $('input[name="overlay"]')
        .on('change click', function () {
            $('#comments-container').toggle(Number(this.value) === 0);
        })
        .filter(':checked').trigger('click');
})(jQuery);
