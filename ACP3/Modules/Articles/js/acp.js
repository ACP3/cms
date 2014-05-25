jQuery(document).ready(function ($) {
    $(':checkbox[name="create"]').bind('onload click',function () {
        var $elem = $('#create-item-container');
        if ($(this).is(':checked')) {
            $elem.show();
        } else {
            $elem.hide();
        }
    }).trigger('onload');

    // Nur die zum Block zugehörigen übergeordneten Seiten anzeigen
    var $parent = $('#parent');
    $parent.find('optgroup').hide();
    $('#block-id').change(function () {
        var block = $('#block-id').find('option:selected').eq(0).text();
        $parent.find('optgroup:not([label=\'' + block + '\'])').hide();
        $parent.find('optgroup[label=\'' + block + '\']').show();
    }).change();
});