<div class="form-group">
    <label for="{$options.0.id}" class="col-sm-2 control-label">
        {$label}
    </label>

    <div class="col-sm-10">
        {foreach $options as $row}
            <div class="checkbox">
                <label for="{$row.id}">
                    <input type="checkbox" name="{$row.name}" id="{$row.id}" value="1"{$row.checked}>
                    {$row.lang}
                </label>
            </div>
        {/foreach}
    </div>
</div>
