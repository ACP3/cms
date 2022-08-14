{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <select class="form-select"
            name="{if isset($name)}{$name}{else}{$options[0].name}{/if}{if isset($multiple) && $multiple === true || isset($is_array) && $is_array === true}[]{/if}"
            id="{if !isset($options[0].id)}{$name|replace:'_':'-'}{else}{$options[0].id}{/if}"
            style="max-height:100px"
            {if isset($multiple) && $multiple === true} multiple{/if}
            {if isset($required) && $required === true} required{if !empty($options)} size="{count($options)}"{/if}{/if}
            {if isset($disabled) && $disabled === true} disabled{/if}
            {if !empty($data_attributes) && is_array($data_attributes)}
                {foreach $data_attributes as $attrName => $attrValue}
                    data-{$attrName}="{$attrValue}"
                {/foreach}
            {/if}>
        {if !isset($required) || $required === false || empty($options)}
            <option value="">
                {if !empty($emptyOptionLabel)}
                    {$emptyOptionLabel}
                {else}
                    {lang t="system|pls_select"}
                {/if}
            </option>
        {/if}
        {block FORM_GROUP_FORM_FIELD_SELECT_OPTIONS}
            {if isset($options)}
                {foreach $options as $row}
                    <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                {/foreach}
            {/if}
        {/block}
    </select>
{/block}
