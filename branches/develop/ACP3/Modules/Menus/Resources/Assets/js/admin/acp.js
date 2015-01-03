$(document).ready(function () {
    var alias = $('#alias').parents('div.control-group');
    var module = $('#module-container');
    var hints = $('#link-hints');
    var link = $('#link-container');
    var articles = $('#articles-container');
    var target = $('#target-container');

    // Wenn Menüpunkt nicht angezeigt werden soll, Linkziel verstecken
    $('input[name="display"]').change(function () {
        if ($(this).val() == 0) {
            target.hide();
        } else {
            target.show();
        }
    });

    var $mode = $('#mode'),
        currentMode = $mode.val();
    // Seitentyp
    $mode.change(function () {
        var mode = $(this).val();

        // SEO Tab bei einem externen Hyperlink deaktivieren
        if (mode == 3) {
            $('.tabbable .nav-tabs a[href="#tab-3"]').addClass('hide');
        } else {
            $('.tabbable .nav-tabs a[href="#tab-3"]').removeClass('hide');
        }

        if (mode == 1) {
            alias.hide();
            module.show();
            hints.hide();
            link.hide();
            articles.hide();

            // Modul im Dropdown-Menü selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
            if (currentMode == 2) {
                var $link = $('#link-uri'),
                    match = $link.val().match(/^([a-z\d_\-]+)\/([a-z\d_\-]+\/)+$/);
                if (!$link.val().match(/^articles\/details\/id_(\d+)\/$/) && match[1] != null && $('#module').find('option[value="' + match[1] + '"]').length > 0) {
                    $('#link-module').val(match[1]);
                }
            }
        } else if (mode == 2) {
            alias.show();
            module.hide();
            hints.show();
            link.show();
            articles.hide();
        } else if (mode == 3) {
            module.hide();
            hints.hide();
            link.show();
            articles.hide();
        } else if (mode == 4) {
            alias.show();
            module.hide();
            hints.hide();
            link.hide();
            articles.show();
        } else {
            alias.hide();
            module.hide();
            hints.hide();
            link.hide();
            articles.hide();
        }

        currentMode = mode;
    }).change();

    $('#link-uri').blur(function () {
        var match = $(this).val().match(/^articles\/index\/details\/id_(\d+)\/$/);
        if (match[1] !== null && $('#articles').find('option[value="' + match[1] + '"]').length > 0) {
            $('#mode').val(4).change();
            $('#link-articles').val(match[1]);
        }
    });

    var $parent = $('#parent');

    // Nur die dem Block zugehörigen übergeordneten Seiten anzeigen
    $parent.find('optgroup').hide();

    var $blockId = $('#block-id'),
        defaultBlock = $blockId.find('option:selected').index() || 0;

    $blockId.change(function () {
        var blockName = $blockId.find('option:selected').eq(0).text();

        $parent.find('optgroup:not([label="' + blockName + '"])')
            .prop('disabled', true)
            .hide();
        $parent.find('optgroup[label="' + blockName + '"]')
            .removeProp('disabled')
            .show();

        $blockId.find('option').each(function (index) {
            if ($(this).is(':selected') && index !== defaultBlock) {
                $parent.find('optgroup option:selected').removeAttr('selected');
            }
        });
    }).triggerHandler('change');
});