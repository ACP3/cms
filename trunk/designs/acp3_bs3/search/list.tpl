{if isset($error_msg)}
	{$error_msg}
{/if}
<form action="{uri args="search/list"}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="form-group">
		<label for="search-term" class="col-lg-2 control-label">{lang t="search|search_term"}</label>
		<div class="col-lg-10"><input class="form-control" type="text" name="search_term" id="search-term" value="{$form.search_term}" required></div>
	</div>
	<div class="form-group">
		<label class="col-lg-2 control-label">{lang t="search|search_after_modules"}</label>
		<div class="col-lg-10">
			<div class="btn-group" data-toggle="buttons">
				{foreach $search_mods as $row}
					<label for="{$row.dir}" class="btn btn-default">
						<input type="checkbox" name="mods[]" id="{$row.dir}" value="{$row.dir}"{$row.checked}>
						{$row.name}
					</label>
				{/foreach}
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="{$search_areas.0.id}" class="col-lg-2 control-label">{lang t="search|search_after_areas"}</label>
		<div class="col-lg-10">
			<div class="btn-group" data-toggle="buttons">
				{foreach $search_areas as $row}
					<label for="{$row.id}" class="btn btn-default">
						<input type="radio" name="area" id="{$row.id}" value="{$row.value}"{$row.checked}>
						{$row.lang}
					</label>
				{/foreach}
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="{$sort_hits.0.id}" class="col-lg-2 control-label">{lang t="search|sort_hits"}</label>
		<div class="col-lg-10">
			<div class="btn-group" data-toggle="buttons">
				{foreach $sort_hits as $row}
					<label for="{$row.id}" class="btn btn-default">
						<input type="radio" name="sort" id="{$row.id}" value="{$row.value}"{$row.checked}>
						{$row.lang}
					</label>
				{/foreach}
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="search|submit_search"}</button>
		</div>
	</div>
</form>