{extends file="asset:System/Partials/form_group.input_base.tpl"}

{block FORM_GROUP_LABEL_ID}for="{$name|replace:'_':'-'}"{/block}
{block FORM_GROUP_FORM_FIELD}
    <input {if isset($use_form_control) && $use_form_control === false}{else}class="{if (isset($readonly) && $readonly === true) || (isset($disabled) && $disabled === true)}form-control-plaintext{else}form-control{/if}"{/if}
           type="{block FORM_GROUP_INPUT_TYPE}text{/block}"
           name="{$name}"
           id="{if isset($formFieldId)}{$formFieldId}{else}{$name|replace:'_':'-'}{/if}"
            {if isset($value)}value="{$value}"{/if}
            {if !empty($minlength)}minlength="{$minlength}"{/if}
            {if !empty($maxlength)}maxlength="{$maxlength}"{/if}
            {if !empty($data_attributes) && is_array($data_attributes)}
                {foreach $data_attributes as $attrName => $attrValue}
                    data-{$attrName}="{$attrValue}"
                {/foreach}
            {/if}
            {if !empty($attributes) && is_array($attributes)}
                {foreach $attributes as $attrName => $attrValue}
                    {$attrName}="{$attrValue}"
                {/foreach}
            {/if}
            {if (isset($required) && $required === true)} required{/if}
            {if (isset($readonly) && $readonly === true)} readonly{/if}
            {if (isset($disabled) && $disabled === true)} disabled{/if}>
{/block}
