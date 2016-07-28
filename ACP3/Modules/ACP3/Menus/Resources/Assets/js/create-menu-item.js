jQuery(document).ready(function ($) {
    var $parent = $('#parent-id');

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