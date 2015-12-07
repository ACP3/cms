jQuery(document).ready(function ($) {
    const $window = $(window);

    $window.on('hashchange', function () {
        const hash = location.hash,
            $link = $('a[href="' + hash + '"]'),
            $element = $(hash);

        if ($link.length) {
            $link.click();
        } else if ($element.length) {
            $element.click();
        }
    });

    if (location.hash) {
        $window.trigger('hashchange');
    }
});