{extends file="asset:layout.tpl"}

{block CONTENT prepend}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="articles|page_statements"}</a></li>
                <li><a href="#tab-3" data-toggle="tab">{lang t="system|seo"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    {include file="asset:system/datepicker.tpl" datepicker=$publication_period}
                </div>
                <div id="tab-2" class="tab-pane fade">
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">{lang t="articles|title"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text" class="col-sm-2 control-label">{lang t="articles|text"}</label>

                        <div class="col-sm-10">{wysiwyg name="text" value="`$form.text`" height="250" advanced="1"}</div>
                    </div>
                    {if isset($options)}
                        <div class="form-group">
                            <label for="{$options.0.id}" class="col-sm-2 control-label">{lang t="system|options"}</label>

                            <div class="col-sm-10">
                                {foreach $options as $row}
                                    <div class="checkbox">
                                        <label for="{$row.id}">
                                            <input type="checkbox" name="create" id="{$row.id}" value="1"{$row.checked}>
                                            {$row.lang}
                                        </label>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                        <div id="create-item-container">
                            <div class="form-group">
                                <label for="block-id" class="col-sm-2 control-label">{lang t="menus|menu_bar"}</label>

                                <div class="col-sm-10">
                                    <select class="form-control" name="block_id" id="block-id">
                                        {foreach $blocks as $row}
                                            <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="parent" class="col-sm-2 control-label">{lang t="menus|superior_page"}</label>

                                <div class="col-sm-10">
                                    <select class="form-control" name="parent" id="parent">
                                        <option value="">{lang t="menus|no_superior_page"}</option>
                                        {foreach $pages_list as $blocks}
                                            <optgroup label="{$blocks.title}">
                                                {foreach $blocks.items as $row}
                                                    <option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
                                                {/foreach}
                                            </optgroup>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="{$display.0.id}" class="col-sm-2 control-label">{lang t="menus|display_item"}</label>

                                <div class="col-sm-10">
                                    <div class="btn-group" data-toggle="buttons">
                                        {foreach $display as $row}
                                            <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                                <input type="radio" name="display" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                                {$row.lang}
                                            </label>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                <div id="tab-3" class="tab-pane fade">
                    {include file="asset:system/seo_fields.tpl" seo=$SEO_FORM_FIELDS}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/articles"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
{/block}

{block JAVASCRIPTS append}
    {include_js module="system" file="datepicker" depends="datetimepicker"}
    {include_js module="articles" file="admin/acp"}
    {include_js module="system" file="forms"}
{/block}