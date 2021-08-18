<label for="{$categories.name}">{lang t="categories|category"}</label>
<select class="form-select" name="{$categories.name}" id="{$categories.name}">
    <option value="">{$categories.custom_text}</option>
    {foreach $categories.categories as $row}
        <option value="{$row.id}"{$row.selected}>{$row.title}</option>
    {/foreach}
</select>
