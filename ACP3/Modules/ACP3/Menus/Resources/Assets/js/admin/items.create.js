(($) => {
    const $module = $('#module-container'),
        $link = $('#link-container'),
        $hints = $link.find('.help-block'),
        $articles = $('#articles-container'),
        $target = $('#target-container'),
        articlesPattern = /^articles\/index\/details\/id_(\d+)\/$/;

    // Wenn Menüpunkt nicht angezeigt werden soll, Linkziel verstecken
    $('input[name="display"]').change(function () {
        const value = Number(this.value);
        $target.toggle(value === 1);

        if (value === 0) {
            // Force the link target to open on the same page programmatically,
            // as the user can not select it for them self
            $('#target').val(1);
        }
    });

    const $mode = $('#mode');
    let currentMode = $mode.val();

    // Seitentyp
    $mode.change(function () {
        const mode = parseInt(this.value);

        if (mode === 1) {
            $module.show();
            $hints.hide();
            $link.hide();
            $articles.hide();

            // Modul im Dropdown-Menü selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
            if (currentMode === 2) {
                const $linkUri = $('#uri'),
                    match = $linkUri.val().match(/^([a-z\d_-]+)\/([a-z\d_-]+\/)+$/);
                if (!$linkUri.val().match(articlesPattern) && match[1] != null && $('#module').find('option[value="' + match[1] + '"]').length > 0) {
                    $('#link-module').val(match[1]);
                }
            }
        } else if (mode === 2) {
            $module.hide();
            $hints.show();
            $link.show();
            $articles.hide();
        } else if (mode === 3) {
            $module.hide();
            $hints.hide();
            $link.show();
            $articles.hide();
        } else {
            $module.hide();
            $hints.hide();
            $link.hide();
            $articles.hide();
        }

        currentMode = mode;
    }).change();

    $('#uri').blur(function () {
        const match = $(this).val().match(articlesPattern);
        if (match[1] !== null && $('#articles').find('option[value="' + match[1] + '"]').length > 0) {
            $('#mode').val(4).change();
            $('#link-articles').val(match[1]);
        }
    });
})(jQuery);
