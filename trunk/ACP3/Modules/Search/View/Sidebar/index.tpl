<form action="{uri args="search"}" method="post" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
    <div class="form-group">
        <input class="form-control" type="text" name="search_term" value="" placeholder="{lang t="search|search_term"}">
    </div>
    <button type="submit" name="submit" class="btn btn-primary">{lang t="search|submit_search"}</button>
    {foreach $search_mods as $row}
        <input type="hidden" name="mods[]" value="{$row.dir}">
    {/foreach}
    <input type="hidden" name="area" value="2">
    <input type="hidden" name="sort" value="asc">
    {$form_token}
</form>