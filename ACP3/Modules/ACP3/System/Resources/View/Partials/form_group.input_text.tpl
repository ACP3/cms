{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_LABEL_ID}{$name|replace:'_':'-'}{/block}
{block FORM_GROUP_FORM_FIELD}
    <input class="form-control"
           type="{block FORM_GROUP_INPUT_TYPE}text{/block}"
           name="{$name}"
           id="{$name|replace:'_':'-'}"
           value="{$value}"
           {if !empty($maxlength)}maxlength="{$maxlength}"{/if}
            {if (isset($required) && $required === true)} required{/if}
            {if (isset($readonly) && $readonly === true)} readonly{/if}
            {if (isset($disabled) && $disabled === true)} disabled{/if}>
{/block}
