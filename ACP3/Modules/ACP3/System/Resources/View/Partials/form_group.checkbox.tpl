{$labelSelectors="pt-0"}

{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    {foreach $options as $row}
        <div class="form-check">
            <input type="checkbox"
                   class="form-check-input"
                   name="{$row.name}"
                   id="{$row.id}"
                   value="{$row.value}"
                    {$row.checked}
                    {if isset($required) && $required === true} required{/if}>
            <label for="{$row.id}" class="form-check-label">
                {$row.lang}
            </label>
        </div>
    {/foreach}
{/block}
