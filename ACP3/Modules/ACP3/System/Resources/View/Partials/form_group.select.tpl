<div class="form-group">
    <label for="{$options.0.id}"
           class="col-sm-2 control-label{if isset($required) && $required === true} required{/if}">
        {$label}
    </label>

    <div class="col-sm-10">
        <select class="form-control"
                name="{$options.0.name}"
                id="{$options.0.id}"
                {if isset($required) && $required === true} required{/if}>
            <option value="">{lang t="system|pls_select"}</option>
            {foreach $options as $row}
                <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
            {/foreach}
        </select>
    </div>
</div>
