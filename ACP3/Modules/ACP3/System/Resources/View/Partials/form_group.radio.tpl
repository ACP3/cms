{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    {foreach $options as $row}
        <div class="radio">
            <label for="{$row.id}">
                <input type="radio"
                       name="{$row.name}"
                       id="{$row.id}"
                       value="{$row.value}"
                        {$row.checked}
                        {if isset($required) && $required === true} required{/if}>
                {$row.lang}
            </label>
        </div>
    {/foreach}
{/block}
