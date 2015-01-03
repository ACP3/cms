jQuery(document).ready(function ($) {
    $(':checkbox[name="create"]').on('click',function () {
        var $elem = $('#create-item-container');
        if ($(this).is(':checked')) {
            $elem.show();
        } else {
            $elem.hide();
        }
    }).triggerHandler('click');

    // Nur die zum Block zugehörigen übergeordneten Seiten anzeigen
    var $parent = $('#parent');
    $parent.find('optgroup').hide();

    $('#block-id').change(function () {
        var blockName = $('#block-id').find('option:selected').eq(0).text();

        $parent.find('optgroup:not([label="' + blockName + '"])')
            .prop('disabled', true)
            .hide();
        $parent.find('optgroup[label="' + blockName + '"]')
            .removeProp('disabled')
            .show();

    }).change();
});