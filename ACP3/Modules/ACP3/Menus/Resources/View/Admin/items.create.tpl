{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
                <li><a href="#tab-2" data-toggle="tab">{lang t="menus|page_type"}</a></li>
                <li><a href="#tab-3" data-toggle="tab">{lang t="seo|seo"}</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in active">
                    <div class="form-group">
                        <label for="mode" class="col-sm-2 control-label required">{lang t="menus|page_type"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="mode" id="mode">
                                {foreach $mode as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label required">{lang t="menus|title"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
                        </div>
                    </div>
                    {include file="asset:menus/create_menu_item.tpl"}
                </div>
                <div id="tab-2" class="tab-pane fade">
                    <div id="module-container" class="form-group">
                        <label for="link-module" class="col-sm-2 control-label required">{lang t="menus|module"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="module" id="link-module">
                                <option value="">{lang t="system|pls_select"}</option>
                                {foreach $modules as $row}
                                    <option value="{$row.dir|lower}"{$row.selected}>{$row.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div id="link-container" class="form-group">
                        <label for="link-uri" class="col-sm-2 control-label required">{lang t="menus|uri"}</label>

                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="uri" id="link-uri" value="{$form.uri}" maxlength="120">

                            <p id="link-hints" class="help-block">{lang t="menus|dynamic_page_hints"}</p>
                        </div>
                    </div>
                    {if isset($articles)}
                        <div id="articles-container" class="form-group">
                            <label for="link-articles" class="col-sm-2 control-label required">{lang t="articles|articles"}</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="articles" id="link-articles">
                                    <option value="">{lang t="system|pls_select"}</option>
                                    {foreach $articles as $row}
                                        <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/if}
                    <div id="target-container" class="form-group">
                        <label for="link-target" class="col-sm-2 control-label required">{lang t="menus|target_page"}</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="target" id="link-target">
                                {foreach $target as $row}
                                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div id="tab-3" class="tab-pane fade">
                    {include file="asset:seo/seo_fields.tpl" seo=$SEO_FORM_FIELDS}
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/menus"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="menus" file="admin/acp"}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}
