<span style="white-space:nowrap">
	<select name="{$categories.name}" id="{$categories.name}"{if isset($categories.create)} style="width:68%;display:inline"{/if}>
		<option value="">{lang t="common|pls_select"}</option>
{foreach $categories.categories as $row}
		<option value="{$row.id}"{$row.selected}>{$row.name}</option>
{/foreach}
	</select>
{if isset($categories.create)}
	- <input type="text" name="{$categories.create.name}" id="{$categories.create.name|replace:'_':'-'}" value="{$categories.create.value}" style="width:29%;display:inline">
{/if}
</span>
