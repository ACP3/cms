<div class="row">
    <div class="col-sm-{if isset($categories.create)}6{else}12{/if}">
        {include file="asset:Categories/Partials/list.tpl" categories=$categories}
    </div>
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
