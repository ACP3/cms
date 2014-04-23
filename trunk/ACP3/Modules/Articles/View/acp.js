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
    $('#parent optgroup').hide();
    $('#block-id').change(function () {
        var block = $('#block-id option:selected').eq(0).text();
        $('#parent optgroup:not([label=\'' + block + '\'])').hide();
        $('#parent optgroup[label=\'' + block + '\']').show();
    }).change();
});