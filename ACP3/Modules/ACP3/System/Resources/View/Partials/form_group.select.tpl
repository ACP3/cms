<div class="form-group">
    <label for="{$options.0.id}"
           class="col-sm-2 control-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}">
        {$label}
    </label>

    <div class="col-sm-10">
        <select class="form-control"
                name="{$options.0.name}"
                id="{$options.0.id}"
                {if isset($required) && $required === true} required{/if}
                {if isset($disabled) && $disabled === true} disabled{/if}>
            {if !isset($required) || $required === false}
                <option value="">
                    {if !empty($emptyOptionLabel)}
                        {$emptyOptionLabel}
                    {else}
                        {lang t="system|pls_select"}
                    {/if}
                </option>
            {/if}
            {foreach $options as $row}
                <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
            {/foreach}
        </select>
    </div>
</div>
