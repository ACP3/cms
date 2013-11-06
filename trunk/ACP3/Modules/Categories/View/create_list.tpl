<div class="row">
    <div class="col-lg-{if isset($categories.create)}6{else}12{/if}">
        <select class="form-control" name="{$categories.name}" id="{$categories.name}">
            <option value="">{$categories.custom_text}</option>
            {foreach $categories.categories as $row}
                <option value="{$row.id}"{$row.selected}>{$row.title}</option>
            {/foreach}
        </select>
    </div>
    {if isset($categories.create)}
        <div class="col-lg-6">
            <input class="form-control" type="text" name="{$categories.create.name}" id="{$categories.create.name|replace:'_':'-'}" value="{$categories.create.value}" placeholder="{lang t="categories|acp_create"}">
        </div>
    {/if}
</div>
