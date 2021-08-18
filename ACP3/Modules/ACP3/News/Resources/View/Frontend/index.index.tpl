{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($categories)}
        <div class="card mb-3">
            <div class="card-header">
                <form action="{uri args="news"}" method="post" class="d-flex justify-content-center align-items-center">
                    <label for="{$categories.name}" class="form-label me-2 mb-0">{lang t="categories|category"}</label>
                    <select class="form-select me-2" name="{$categories.name}" id="{$categories.name}">
                        <option value="">{$categories.custom_text}</option>
                        {foreach $categories.categories as $row}
                            <option value="{$row.id}"{$row.selected}>{$row.title}</option>
                        {/foreach}
                    </select>
                    <button type="submit" name="submit" class="btn btn-outline-primary">{lang t="system|submit"}</button>
                </form>
            </div>
        </div>
    {/if}
    {if !empty($news)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $news as $row}
            <article class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{uri args="news/index/details/id_`$row.id`"}">{$row.title}</a>
                    <time class="badge bg-primary rounded-pill" datetime="{date_format date=$row.start format="c"}">
                        {date_format date=$row.start format=$dateformat}
                    </time>
                </div>
                <div class="card-body">
                    {$row.text|rewrite_uri}
                    {event name="news.layout.item_index_after" news=$row}
                </div>
            </article>
        {/foreach}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}

    {$smarty.block.parent}
{/block}
