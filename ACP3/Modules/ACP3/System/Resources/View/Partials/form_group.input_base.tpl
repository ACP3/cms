{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    {if !empty($input_group_before)}
        <div class="input-group">
            <span class="input-group-text">{$input_group_before}</span>
            {$smarty.block.child}
        </div>
    {elseif !empty($input_group_after)}
        <div class="input-group">
            {$smarty.block.child}
            <span class="input-group-text">{$input_group_after}</span>
        </div>
    {else}
        {$smarty.block.child}
    {/if}
{/block}
