/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

(($) => {
    $('#rating-wrapper')
        .on('change', ':radio', function () {
            $('.rating__average').remove();
            $('.rating__already-rated').remove();
            $(this).closest('form').submit();
        })
        .on('click', '.rating__average .rating__star', function () {
            // .rating__average sets the direction back to ltr, so we have to account for this
            const index = 4 - $(this).index();
            $('.rating > .rating__star')
                .eq(index)
                .addClass('rating__star_active')
                .click();
        });
})(jQuery);
