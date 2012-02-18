{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="search/list"}" method="post" accept-charset="UTF-8">
	<fieldset class="no-border">
		<dl>
			<dt><label for="search-term">{lang t="search|search_term"}</label></dt>
			<dd><input type="text" name="form[search_term]" id="search-term" value="{$form.search_term}" required></dd>
		</dl>
		<fieldset>
			<legend>{lang t="search|search_options"}</legend>
			<dl>
				<dt><label>{lang t="search|search_after_modules"}</label></dt>
				<dd>
{foreach $search_mods as $row}
					<label for="{$row.dir}">
						<input type="checkbox" name="form[mods][]" id="{$row.dir}" value="{$row.dir}"{$row.checked} class="checkbox">
						{$row.name}
					</label>
{/foreach}
				</dd>
				<dt><label for="{$search_areas.0.id}">{lang t="search|search_after_areas"}</label></dt>
				<dd>
{foreach $search_areas as $row}
					<label for="{$row.id}">
						<input type="radio" name="form[area]" id="{$row.id}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt><label for="{$sort_hits.0.id}">{lang t="search|sort_hits"}</label></dt>
				<dd>
{foreach $sort_hits as $row}
					<label for="{$row.id}">
						<input type="radio" name="form[sort]" id="{$row.id}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</fieldset>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="search|submit_search"}" class="form">
	</div>
</form>