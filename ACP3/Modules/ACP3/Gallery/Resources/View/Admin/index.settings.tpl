{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="gallery|image_dimensions"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
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
                    <div class="form-group">
                        <label for="thumbwidth" class="col-sm-2 control-label required">{lang t="gallery|thumb_image_width"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" name="thumbwidth" id="thumbwidth" value="{$form.thumbwidth}" required>

                            <p class="help-block">{lang t="system|statements_in_pixel"}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="thumbheight" class="col-sm-2 control-label required">{lang t="gallery|thumb_image_height"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" name="thumbheight" id="thumbheight" value="{$form.thumbheight}" required>

                            <p class="help-block">{lang t="system|statements_in_pixel"}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="width" class="col-sm-2 control-label required">{lang t="gallery|image_width"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" name="width" id="width" value="{$form.width}" required>

                            <p class="help-block">{lang t="system|statements_in_pixel"}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="height" class="col-sm-2 control-label required">{lang t="gallery|image_height"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="number" name="height" id="height" value="{$form.height}" required>

                            <p class="help-block">{lang t="system|statements_in_pixel"}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/gallery"}}
    </form>
    {javascripts}
        {include_js module="gallery" file="admin/index.settings"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
