(($) => {
    const $module = $('#module-container'),
        $link = $('#link-container'),
        $hints = $link.find('.help-block'),
        $target = $('#target-container');

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

            // Modul im Dropdown-Menü selektieren, falls zuvor als Modus eine dynamische Seite aktiv war
            if (currentMode === 2) {
                const $linkUri = $('#uri'),
                    match = $linkUri.val().match(/^([a-z\d_-]+)\/([a-z\d_-]+\/)+$/);
                if (match[1] != null && $('#module').find('option[value="' + match[1] + '"]').length > 0) {
                    $('#link-module').val(match[1]);
                }
            }
        } else if (mode === 2) {
            $module.hide();
            $hints.show();
            $link.show();
        } else if (mode === 3) {
            $module.hide();
            $hints.hide();
            $link.show();
        }

        currentMode = mode;
    }).change();
})(jQuery);
