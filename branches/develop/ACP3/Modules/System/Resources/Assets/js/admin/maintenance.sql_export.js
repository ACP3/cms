/**
 * Marks options as selected
 *
 * @param action
 */
function mark_options(action) {
    var $elem = $('form #tables option');
    if (action === 'add') {
        $elem.attr('selected', true);
    } else {
        $elem.removeAttr('selected');
    }
}

$(function () {
    $('input[name="export_type"]').bind('click',function () {
        var $elem = $('#options-container');
        if ($(this).attr('id') === 'complete' || $(this).attr('id') === 'structure') {
            $elem.show();
        } else {
            $elem.hide();
        }
    }).filter(':checked').trigger('click');
});
