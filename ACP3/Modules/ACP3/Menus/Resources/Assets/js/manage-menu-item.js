jQuery(document).ready(function ($) {
    var $parentId = $('#parent-id'),
        $blockId = $('#block-id'),
        defaultBlockIndex = $blockId.find('option:selected').index() || 0;

    $(':checkbox[name="create"]').on('click', function () {
        $('#manage-menu-item-container').toggle(this.checked);
    }).triggerHandler('click');

    $blockId.change(function () {
        var blockName = $blockId.find('option:selected').eq(0).text();

        $parentId.find('optgroup')
            .prop('disabled', true)
            .hide();
        $parentId.find('optgroup[label="' + blockName + '"]')
            .prop('disabled', false)
            .show();

        $blockId.find('option').each(function (index) {
            if ($(this).is(':selected') && index !== defaultBlockIndex) {
                $parentId.find('optgroup option:selected').removeAttr('selected');
            }
        });
    }).triggerHandler('change');
});
