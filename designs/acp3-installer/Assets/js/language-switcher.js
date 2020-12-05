/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

(($) => {
    const $doc = $(document),
        $languages = $('#languages');

    $doc.data('has-changes', false);
    $('#content').find(':input').change(() => {
        $doc.data('has-changes', true);
    });

    $languages.find('.btn').addClass('hidden');
    $('#lang').change(function () {
        let submitForm = true;
        if ($doc.length > 0 && $doc.data('has-changes') === true) {
            submitForm = confirm($(this).data('change-language-warning'));
        }

        if (submitForm === true) {
            $languages.submit();
        }
    });
})(jQuery);
