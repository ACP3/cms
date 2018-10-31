{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a href="#tab-1" class="nav-link active" data-toggle="tab">{lang t="system|general_statements"}</a></li>
            <li class="nav-item"><a href="#tab-2" class="nav-link" data-toggle="tab">{lang t="gallery|image_dimensions"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show active">
                {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
                {include file="asset:System/Partials/form_group.select.tpl" options=$sidebar_entries required=true label={lang t="system|sidebar_entries_to_display"}}
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$overlay required=true label={lang t="gallery|use_overlay"} help={lang t="gallery|use_overlay_description"}}
                {if isset($comments)}
                    <div id="comments-container">
                        {include file="asset:System/Partials/form_group.button_group.tpl" options=$comments required=true label={lang t="system|allow_comments"}}
                    </div>
                {/if}
            </div>
            <div id="tab-2" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_number.tpl" name="thumbwidth" value=$form.thumbwidth required=true label={lang t="gallery|thumb_image_width"} input_group_after={lang t="system|pixel"}}
                {include file="asset:System/Partials/form_group.input_number.tpl" name="thumbheight" value=$form.thumbheight required=true label={lang t="gallery|thumb_image_height"} input_group_after={lang t="system|pixel"}}
                {include file="asset:System/Partials/form_group.input_number.tpl" name="width" value=$form.width required=true label={lang t="gallery|image_width"} input_group_after={lang t="system|pixel"}}
                {include file="asset:System/Partials/form_group.input_number.tpl" name="height" value=$form.height required=true label={lang t="gallery|image_height"} input_group_after={lang t="system|pixel"}}
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/gallery"}}
    {javascripts}
        {include_js module="gallery" file="admin/index.settings"}
    {/javascripts}
{/block}
