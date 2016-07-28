<div id="create-menu-item-container">
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
    <div class="form-group">
        <label for="{$display.0.id}" class="col-sm-2 control-label required">{lang t="menus|display_item"}</label>

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
{javascripts}
    {include_js module="menus" file="create-menu-item"}
{/javascripts}
