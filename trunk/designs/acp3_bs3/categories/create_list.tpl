<select class="form-control" name="{$categories.name}" id="{$categories.name}">
	<option value="">{$categories.custom_text}</option>
{foreach $categories.categories as $row}
	<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
</select>
{if isset($categories.create)}
- <input class="form-control" type="text" name="{$categories.create.name}" id="{$categories.create.name|replace:'_':'-'}" value="{$categories.create.value}">
{/if}