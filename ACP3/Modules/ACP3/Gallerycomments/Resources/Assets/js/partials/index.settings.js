(($, document) => {
    $('input[name="overlay"]')
        .on('change click', function () {
            document.getElementById('comments-container').classList.toggle('hidden', Number(this.value) !== 0);
        })
        .filter(':checked').trigger('click');
})(jQuery, document);
