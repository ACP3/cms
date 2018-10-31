{extends file="asset:System/Partials/form_group.base.tpl"}

{block FORM_GROUP_FORM_FIELD}
    {if !empty($input_group_before)}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">{$input_group_before}</span>
            </div>
            {$smarty.block.child}
        </div>
    {elseif !empty($input_group_after)}
        <div class="input-group">
            {$smarty.block.child}
            <div class="input-group-append">
                <span class="input-group-text">{$input_group_after}</span>
            </div>
        </div>
    {else}
        {$smarty.block.child}
    {/if}
{/block}
