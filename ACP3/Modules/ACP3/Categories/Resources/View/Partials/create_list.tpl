<div class="row">
    {if (!empty($categories.categories))}
        <div class="col-sm mb-2 mb-sm-0">
            <select class="form-select" name="{$categories.name}" id="{$categories.name}" aria-label="{lang t="categories|categories"}">
                <option value="">{$categories.custom_text}</option>
                {foreach $categories.categories as $row}
                    <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                {/foreach}
            </select>
        </div>
    {/if}
    {if !empty($categories.create)}
        <div class="col-sm">
            <input class="form-control"
                   type="text"
                   name="{$categories.create.name}"
                   id="{$categories.create.name|replace:'_':'-'}"
                   value="{$categories.create.value}"
                   placeholder="{lang t="categories|create"}">
        </div>
    {/if}
</div>
