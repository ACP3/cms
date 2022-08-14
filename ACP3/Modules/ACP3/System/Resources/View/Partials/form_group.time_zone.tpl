{extends file="asset:System/Partials/form_group.select.tpl"}

{block FORM_GROUP_FORM_FIELD_SELECT_OPTIONS}
    {foreach $time_zones as $continent => $countries}
        <optgroup label="{$continent}">
            {foreach $countries as $country => $data}
                <option value="{$country}"{$data.selected}>{$country}</option>
            {/foreach}
        </optgroup>
    {/foreach}
{/block}
