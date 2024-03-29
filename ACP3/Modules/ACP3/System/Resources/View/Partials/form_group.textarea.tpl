{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_LABEL_ID}for="{$name|replace:'_':'-'}"{/block}
{block FORM_GROUP_FORM_FIELD}
    {if !empty($before_textarea)}
        {$before_textarea}
    {/if}
    <textarea class="{if (isset($readonly) && $readonly === true) || (isset($disabled) && $disabled === true)}form-control-plaintext{else}form-control{/if}"
              name="{$name}"
              id="{if isset($formFieldId)}{$formFieldId}{else}{$name|replace:'_':'-'}{/if}"
              cols="60"
              rows="6"
            {if (isset($required) && $required === true)} required{/if}
            {if (isset($readonly) && $readonly === true)} readonly{/if}
            {if (isset($disabled) && $disabled === true)} disabled{/if}>{if isset($value)}{$value}{/if}</textarea>
{/block}
