<form action="{uri args="search/list"}" method="post" accept-charset="UTF-8" class="navbar-search pull-right">
	<div class="controls"><input type="text" name="search_term" value="{$form.search_term}" placeholder="{lang t="search|search_term"}" class="search-query"></div>
	<button type="submit" name="submit" class="btn hide">{lang t="search|submit_search"}</button>
{foreach $search_mods as $row}
	<input type="hidden" name="mods[]" value="{$row.dir}">
{/foreach}
	<input type="hidden" name="area" value="2">
	<input type="hidden" name="sort" value="asc">
</form>