<label for="{$categories.name}" class="sr-only">{lang t="categories|category"}</label>
<select class="form-control" name="{$categories.name}" id="{$categories.name}">
    <option value="">{$categories.custom_text}</option>
    {foreach $categories.categories as $row}
        <option value="{$row.id}"{$row.selected}>{$row.title}</option>
    {/foreach}
</select>
