<div class="row mb-3">
    {if (!empty($categories.categories))}
        <div class="col-sm-{if isset($categories.create)}6{else}12{/if}">
            <select class="form-select" name="{$categories.name}" id="{$categories.name}" aria-label="{lang t="categories|categories"}">
                <option value="">{$categories.custom_text}</option>
                {foreach $categories.categories as $row}
                    <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                {/foreach}
            </select>
        </div>
    {/if}
    {if !empty($categories.create)}
        <div class="col-sm-6">
            <input class="form-control"
                   type="text"
                   name="{$categories.create.name}"
                   id="{$categories.create.name|replace:'_':'-'}"
                   value="{$categories.create.value}"
                   placeholder="{lang t="categories|create"}">
        </div>
    {/if}
</div>
