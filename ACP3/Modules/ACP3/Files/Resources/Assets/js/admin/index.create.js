(($) => {
    $(':checkbox[name="external"]')
        .on('click', function () {
            $('#file-external-toggle').toggle($(this).is(':checked'));
            $('#file-internal-toggle').toggle(!$(this).is(':checked'));
        })
        .triggerHandler('click');
})(jQuery);
