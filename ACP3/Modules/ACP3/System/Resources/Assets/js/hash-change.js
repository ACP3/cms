jQuery(document).ready(function ($) {
    var $window = $(window);

    $window.on('hashchange', function () {
        var hash = location.hash,
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
