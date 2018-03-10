/*
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

jQuery(document).ready(function($) {
    $('[data-add-tab-identifier]').each(function() {
        var tabIdentifier = $(this).data('add-tab-identifier'),
            $element = $(tabIdentifier),
            $tabContent = $element.closest('.tab-content'),
            $tabs = $tabContent.prev('.nav-tabs'),
            tabTitle = $(this).data('add-tab-title'),
            newTabItem = '<li><a href="' +  tabIdentifier + '" data-toggle="tab">' + tabTitle + '</a></li>';

        $tabs.append(newTabItem);
    });
});
