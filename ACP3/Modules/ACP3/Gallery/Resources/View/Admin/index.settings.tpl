{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="gallery-admin-settings-form"}
        {tab title={lang t="system|general_statements"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$sidebar_entries required=true label={lang t="system|sidebar_entries_to_display"}}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$overlay required=true label={lang t="gallery|use_overlay"}}
            {event name="gallery.layout.settings"}
        {/tab}
        {tab title={lang t="gallery|image_dimensions"}}
            {include file="asset:System/Partials/form_group.input_number.tpl" name="thumbwidth" value=$form.thumbwidth required=true label={lang t="gallery|thumb_image_width"} input_group_after={lang t="system|pixel"}}
            {include file="asset:System/Partials/form_group.input_number.tpl" name="thumbheight" value=$form.thumbheight required=true label={lang t="gallery|thumb_image_height"} input_group_after={lang t="system|pixel"}}
            {include file="asset:System/Partials/form_group.input_number.tpl" name="width" value=$form.width required=true label={lang t="gallery|image_width"} input_group_after={lang t="system|pixel"}}
            {include file="asset:System/Partials/form_group.input_number.tpl" name="height" value=$form.height required=true label={lang t="gallery|image_height"} input_group_after={lang t="system|pixel"}}
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/gallery"}}
{/block}
