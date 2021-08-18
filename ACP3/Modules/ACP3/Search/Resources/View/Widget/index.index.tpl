<form action="{uri args="search"}" method="post" accept-charset="UTF-8" class="d-flex" role="search">
    <input class="form-control me-2"
           type="search"
           name="search_term"
           aria-label="{lang t="search|search_term"}"
           value=""
           placeholder="{lang t="search|search_term"}"
           required>
    <button type="submit" name="submit" class="btn btn-outline-primary" title="{lang t="search|submit_search"}">
        {icon iconSet="solid" icon="search"}
    </button>
    {foreach $search_mods as $row}
        <input type="hidden" name="mods[]" value="{$row.name}">
    {/foreach}
    <input type="hidden" name="area" value="title_content">
    <input type="hidden" name="sort" value="asc">
</form>
