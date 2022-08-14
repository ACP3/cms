{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    <select class="form-select"
            name="{if isset($name)}{$name}{else}{$options[0].name}{/if}{if isset($multiple) && $multiple === true || isset($is_array) && $is_array === true}[]{/if}"
            id="{if !isset($options[0].id)}{$name|replace:'_':'-'}{else}{$options[0].id}{/if}"
            {if isset($required) && $required === true} required{if !empty($options)} size="{count($options)}"{/if}{/if}>
        {foreach $time_zones as $continent => $countries}
            <optgroup label="{$continent}">
                {foreach $countries as $country => $data}
                    <option value="{$country}"{$data.selected}>{$country}</option>
                {/foreach}
            </optgroup>
        {/foreach}
    </select>
{/block}
