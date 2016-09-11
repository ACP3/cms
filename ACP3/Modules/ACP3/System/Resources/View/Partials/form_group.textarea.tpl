{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_LABEL_ID}for="{$name|replace:'_':'-'}"{/block}
{block FORM_GROUP_FORM_FIELD}
    {if !empty($before_textarea)}
        {$before_textarea}
    {/if}
    <textarea class="form-control"
              name="{$name}"
              id="{$name|replace:'_':'-'}"
              cols="60"
              rows="6"
            {if (isset($required) && $required === true)} required{/if}
            {if (isset($readonly) && $readonly === true)} readonly{/if}
            {if (isset($disabled) && $disabled === true)} disabled{/if}>{if !empty($value)}{$value}{/if}</textarea>
{/block}
