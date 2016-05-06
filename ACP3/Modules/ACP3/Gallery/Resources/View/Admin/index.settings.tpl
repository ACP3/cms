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
                    <div class="form-group">
                        <label for="date-format" class="col-sm-2 control-label required">{lang t="system|date_format"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="dateformat" id="date-format" required>
                                {foreach $dateformat as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sidebar-entries" class="col-sm-2 control-label required">{lang t="system|sidebar_entries_to_display"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="sidebar" id="sidebar-entries" required>
                                {foreach $sidebar_entries as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="{$overlay.0.id}" class="col-sm-2 control-label required">{lang t="gallery|use_overlay"}</label>

                        <div class="col-sm-10">
                            <div class="btn-group" data-toggle="buttons">
                                {foreach $overlay as $row}
                                    <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                        <input type="radio" name="overlay" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                        {$row.lang}
                                    </label>
                                {/foreach}
                            </div>
                            <p class="help-block">{lang t="gallery|use_overlay_description"}</p>
                        </div>
                    </div>
                    {if isset($comments)}
                        <div id="comments-container" class="form-group">
                            <label for="{$comments.0.id}" class="col-sm-2 control-label required">{lang t="system|allow_comments"}</label>

                            <div class="col-sm-10">
                                <div class="btn-group" data-toggle="buttons">
                                    {foreach $comments as $row}
                                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                            <input type="radio" name="comments" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                            {$row.lang}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
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
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/gallery"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="gallery" file="admin/index.settings"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
