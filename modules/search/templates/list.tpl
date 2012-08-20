{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="search/list"}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="search-term" class="control-label">{lang t="search|search_term"}</label>
		<div class="controls"><input type="text" name="search_term" id="search-term" value="{$form.search_term}" required></div>
	</div>
	<div class="control-group">
		<label class="control-label">{lang t="search|search_after_modules"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="checkbox">
{foreach $search_mods as $row}
				<input type="checkbox" name="mods[]" id="{$row.dir}" value="{$row.dir}"{$row.checked}>
				<label for="{$row.dir}" class="btn">{$row.name}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="{$search_areas.0.id}" class="control-label">{lang t="search|search_after_areas"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $search_areas as $row}
				<input type="radio" name="area" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="control-group">
		<label for="{$sort_hits.0.id}" class="control-label">{lang t="search|sort_hits"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $sort_hits as $row}
				<input type="radio" name="sort" id="{$row.id}" value="{$row.value}"{$row.checked}>
				<label for="{$row.id}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="search|submit_search"}</button>
	</div>
</form>