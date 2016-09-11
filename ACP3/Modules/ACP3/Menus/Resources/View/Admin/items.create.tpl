{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="menus|page_type"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {include file="asset:System/Partials/form_group.select.tpl" options=$mode required=true label={lang t="menus|page_type"}}
                {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 label={lang t="menus|title"}}
                {include file="asset:Menus/Partials/create_menu_item.tpl"}
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
                <div id="target-container">
                    {include file="asset:System/Partials/form_group.select.tpl" options=$target labelRequired=true label={lang t="menus|target_page"}}
                </div>
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/menus"}}
    {javascripts}
        {include_js module="menus" file="admin/acp"}
    {/javascripts}
{/block}
