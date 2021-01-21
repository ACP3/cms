{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="menu-item-admin-edit-form"}
        {tab title={lang t="system|general_statements"}}
            {include file="asset:System/Partials/form_group.select.tpl" options=$mode required=true label={lang t="menus|page_type"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="title" value=$form.title required=true maxlength=120 label={lang t="menus|title"}}
            <div class="form-group">
                <label for="block-id" class="col-sm-2 control-label required">{lang t="menus|menu_bar"}</label>

                <div class="col-sm-10">
                    <select class="form-control" name="block_id" id="block-id" required>
                        {foreach $blocks as $row}
                            <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="parent-id" class="col-sm-2 control-label required">{lang t="menus|superior_page"}</label>

                <div class="col-sm-10">
                    <select class="form-control" name="parent_id" id="parent-id">
                        <option value="">{lang t="menus|no_superior_page"}</option>
                        {foreach $menuItems as $blocks}
                            <optgroup label="{$blocks.title}">
                                {foreach $blocks.items as $row}
                                    <option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    </select>
                </div>
            </div>
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$display required=true label={lang t="menus|display_item"}}
        {/tab}
        {tab title={lang t="menus|page_type"}}
            <div id="module-container" class="form-group">
                <label for="link-module" class="col-sm-2 control-label required">{lang t="menus|module"}</label>

                <div class="col-sm-10">
                    <select class="form-control" name="module" id="link-module">
                        <option value="">{lang t="system|pls_select"}</option>
                        {foreach $modules as $row}
                            <option value="{$row.name|lower}"{$row.selected}>{lang t="`$row.name`|`$row.name`"}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div id="link-container">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="uri" value=$form.uri labelRequired=true maxlength=120 label={lang t="menus|uri"} help={lang t="menus|dynamic_page_hints"}}
            </div>
            <div id="target-container">
                {include file="asset:System/Partials/form_group.select.tpl" options=$target labelRequired=true label={lang t="menus|target_page"}}
            </div>
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/menus"}}
    {javascripts}
        {include_js module="menus" file="admin/items.create"}
        {include_js module="menus" file="partials/manage-menu-item"}
    {/javascripts}
{/block}
