{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_LABEL_ID}{/block}
{block FORM_GROUP_FORM_FIELD}
    <div class="form-control-static">
        {if isset($value)}
            {$value}
        {/if}
    </div>
{/block}
