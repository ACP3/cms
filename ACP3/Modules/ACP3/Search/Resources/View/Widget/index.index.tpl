<form action="{uri args="search"}" method="post" accept-charset="UTF-8" class="mt-2 mt-sm-0 form-inline ml-auto" role="search">
    <label for="widget-search-term" class="sr-only">{lang t="search|search_term"}</label>
    <div class="input-group">
        <input class="form-control"
               type="text"
               name="search_term"
               id="widget-search-term"
               value=""
               placeholder="{lang t="search|search_term"}"
               required>
        <div class="input-group-append">
            <button type="submit" name="submit" class="btn btn-primary" title="{lang t="search|submit_search"}">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    {foreach $search_mods as $row}
        <input type="hidden" name="mods[]" value="{$row.dir}">
    {/foreach}
    <input type="hidden" name="area" value="title_content">
    <input type="hidden" name="sort" value="asc">
</form>
