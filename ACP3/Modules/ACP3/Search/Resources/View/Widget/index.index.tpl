<form action="{uri args="search"}" method="post" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
    <div class="form-group">
        <label for="widget-search-term" class="sr-only">{lang t="search|search_term"}</label>
        <input class="form-control"
               type="text"
               name="search_term"
               id="widget-search-term"
               value=""
               placeholder="{lang t="search|search_term"}"
               required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary" title="{lang t="search|submit_search"}">
        {icon iconSet="solid" icon="search"}
    </button>
    {foreach $search_mods as $row}
        <input type="hidden" name="mods[]" value="{$row.name}">
    {/foreach}
    <input type="hidden" name="area" value="title_content">
    <input type="hidden" name="sort" value="asc">
</form>
