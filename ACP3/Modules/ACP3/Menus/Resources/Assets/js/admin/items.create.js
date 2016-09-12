jQuery(document).ready(function ($) {
    var $module = $('#module-container'),
        $link = $('#link-container'),
        $hints = $link.find('.help-block'),
        $articles = $('#articles-container'),
        $target = $('#target-container'),
        articlesPattern = /^articles\/index\/details\/id_(\d+)\/$/;

    // Wenn Menüpunkt nicht angezeigt werden soll, Linkziel verstecken
    $('input[name="display"]').change(function () {
        $target.toggle(this.value == 1);
    });

    var $mode = $('#mode'),
        currentMode = $mode.val();

    // Seitentyp
    $mode.change(function () {
        var mode = this.value;

        if (mode == 1) {
            $module.show();
            $hints.hide();
            $link.hide();
            $articles.hide();

            // Modul im Dropdown-Menü selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
            if (currentMode == 2) {
                var $linkUri = $('#uri'),
                    match = $linkUri.val().match(/^([a-z\d_\-]+)\/([a-z\d_\-]+\/)+$/);
                if (!$linkUri.val().match(articlesPattern) && match[1] != null && $('#module').find('option[value="' + match[1] + '"]').length > 0) {
                    $('#link-module').val(match[1]);
                }
            }
        } else if (mode == 2) {
            $module.hide();
            $hints.show();
            $link.show();
            $articles.hide();
        } else if (mode == 3) {
            $module.hide();
            $hints.hide();
            $link.show();
            $articles.hide();
        } else if (mode == 4) {
            $module.hide();
            $hints.hide();
            $link.hide();
            $articles.show();
        } else {
            $module.hide();
            $hints.hide();
            $link.hide();
            $articles.hide();
        }

        currentMode = mode;
    }).change();

    $('#uri').blur(function () {
        var match = $(this).val().match(articlesPattern);
        if (match[1] !== null && $('#articles').find('option[value="' + match[1] + '"]').length > 0) {
            $('#mode').val(4).change();
            $('#link-articles').val(match[1]);
        }
    });
});
